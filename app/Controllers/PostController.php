<?php

// app/Controller/PostController

require_once __DIR__ . "/../Models/Posts.php";
require_once __DIR__ . "/../Models/Stickers.php";
require_once __DIR__ . "/../Models/Likes.php";
require_once __DIR__ . "/../Models/Comments.php";

class PostController
{
    private Post $post;
    private Sticker $sticker;
    private Like $like;
    private Comment $comment;

    public function __construct() {
        $this->post = new Post();
        $this->sticker = new Sticker();
        $this->like = new Like();
        $this->comment = new Comment();
    }

    public function show() {
        ob_end_clean();
        header('Content-Type: application/json');
        $postId = (int) ($_GET['id'] ?? 0);
        if (!$postId) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid post id']);
            return ;
        }
        $user = $_SESSION['user'] ?? null;
        $post = $this->post->getById($postId);
        if (!$post) {
            http_response_code(404);
            echo json_encode(['error' => ' Post not found']);
            return ;
        }

        $comments = $this->comment->getByPostId($postId);
        $hasLiked = $user ? $this->like->hasLiked($postId, $user['id']) : false;
        $likeCount = $this->post->getLikeCount($postId);

        echo json_encode([
            'post'     => $post,
            'comments'  => $comments,
            'hasLiked'  => $hasLiked,
            'likeCount' => $likeCount
        ]);
    }

    public function like() {
        ob_end_clean();
        header('Content-Type: application/json');

        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return ;
        }

        $postId = (int) ($_POST['post_id'] ?? 0);
        if (!$postId) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid post id']);
            return ;
        }

        $userId = $_SESSION['user']['id'];
        $this->like->toggle($postId, $userId);

        $hasLiked = $this->like->hasLiked($postId, $userId);
        $likeCount = $this->post->getLikeCount($postId);

        echo json_encode([
            'hasLiked' => $hasLiked,
            'likeCount' => $likeCount
        ]);
    }

    public function comment() {
        ob_end_clean();
        header('Content-Type: application/json');

        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return ;
        }

        $postId = (int) ($_POST['post_id'] ?? 0);
        $content = trim($_POST['content'] ?? '');
        if (!$postId || empty($content)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid data']);
            return ;
        }
        $userId = $_SESSION['user']['id'];
        $this->comment->create($postId, $userId, $content);

        $comments = $this->comment->getByPostId($postId);

        echo json_encode(['comments' => $comments]);
    }

    public function create(string $method) {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        $user = $_SESSION['user'];
        $errors = [];

        if ($method === 'POST') {
            $description    = trim($_POST['description'] ?? '');
            $imageData      = $_POST['image_data'] ?? null;
            $stickersData   = $_POST['stickers'] ?? null;
            $stickers = json_decode($stickersData, true);

            if (!is_array($stickers) || empty($stickers))
                $errors[] = "Please select at least one sticker.";
            if (!$imageData)
                $errors[] = "No image captured.";
            if (is_array($stickers)) {
                foreach ($stickers as $sticker) {
                    if (!isset(
                        $sticker['id'],
                        $sticker['x'],
                        $sticker['y'],
                        $sticker['w'],
                        $sticker['h'],
                        $sticker['angle']
                    )) {
                        $errors[] = "Invalid sticker data.";
                        break ;
                    }
                }
            }
            if (empty($errors)) {
                $filename = uniqid('post_', true) . '.jpeg';
                $destPath = __DIR__ . '/../../public/uploads/posts/' . $filename;
                $publicPath = '/uploads/posts/' . $filename;
                if (count($stickers) === 1) {
                    $success = $this->compositeImage($imageData, $stickers[0], $user['id'], $destPath);
                } else {
                    // ===== BONUS =====
                    $success = $this->compositeComplexImage(
                        $imageData,
                        $stickers,
                        $user['id'],
                        $destPath
                    );
                    // ===== BONUS =====
                }
                if ($success && $this->post->save($user['id'], $filename, $publicPath, $description)) {
                    header("Location: /create");
                    exit;
                } else {
                    $errors[] = "Failed to process image";
                }
            }
        }
        $stickers = $this->sticker->getAvailable($user['id']);
        $userPosts = $this->post->getByUserId($user['id']);

        $title = "Camagru - Creation";
        $viewContent = __DIR__ . '/../Views/create.php';
        require __DIR__ . '/../Views/layout.php';
    }

    private function getStickerPath(int $stickerId, int $userId): ?string {
        $stickers = $this->sticker->getAvailable($userId);
        foreach ($stickers as $sticker) {
            if ((int) $sticker['id'] === $stickerId) {
                return __DIR__ . "/../../public" . $sticker['path'];
            }
        }
        return null;
    }

    private function createPictureFromBase64(string $base64): GdImage|false {
        $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $base64);
        $imageData = base64_decode($base64, true);

        if ($imageData === false)
            return false;

        return imagecreatefromstring($imageData);
    }

    private function applySticker(
        GdImage $picture,
        string $stickerPath,
        int $x,
        int $y,
        int $w,
        int $h,
        float $angle
    ): bool {
        $sticker = imagecreatefrompng($stickerPath);
        if (!$sticker)
            return false;

        $stickerOriW = imagesx($sticker);
        $stickerOriH = imagesy($sticker);

        $resizedSticker = imagecreatetruecolor($w, $h);
        if (!$resizedSticker) {
            return false;
        }
        imagealphablending($resizedSticker, false);
        imagesavealpha($resizedSticker, true);
        imagecopyresampled(
            dst_image: $resizedSticker,
            src_image: $sticker,
            dst_x: 0,
            dst_y: 0,
            src_x: 0,
            src_y: 0,
            dst_width: $w,
            dst_height: $h,
            src_width: $stickerOriW,
            src_height: $stickerOriH
        );

        if ($angle != 0.0) {

            $centerX = $x + $w / 2;
            $centerY = $y + $h / 2;

            $transparent = imagecolorallocatealpha($resizedSticker, 0, 0, 0, 127);
            $rotatedSticker = imagerotate($resizedSticker, -$angle, $transparent);

            if (!$rotatedSticker) {
                return false;
            }
            $resizedSticker = $rotatedSticker;
            imagealphablending($resizedSticker, false);
            imagesavealpha($resizedSticker, true);

            $w = imagesx($resizedSticker);
            $h = imagesy($resizedSticker);

            $x = (int) round($centerX - $w / 2);
            $y = (int) round($centerY - $h / 2);
        }

        imagealphablending($picture, true);
        return imagecopy($picture, $resizedSticker, $x, $y, 0, 0, $w, $h);
    }

// =============== BONUS ===================================================================================================

    private function compositeComplexImage(string $base64, array $stickers, int $userId, string $destPath): bool{
        $picture = $this->createPictureFromBase64($base64);

        if (!$picture)
            return false;

        foreach ($stickers as $sticker) {
            $stickerPath = $this->getStickerPath($sticker['id'], $userId);
            if (!$stickerPath)
                return false;

            $success = $this->applySticker(
                $picture,
                $stickerPath,
                $sticker['x'],
                $sticker['y'],
                $sticker['w'],
                $sticker['h'],
                $sticker['angle']
            );
            if (!$success)
                return false;
        }
        return imagejpeg($picture, $destPath, 90);
    }

// =========================================================================================================================


    private function compositeImage(string $base64, array $sticker, int $userId, string $destPath): bool {
        $picture = $this->createPictureFromBase64($base64);
        if (!$picture)
            return false;

        $stickerPath = $this->getStickerPath($sticker['id'], $userId);
        if (!$stickerPath)
            return false;

        $success = $this->applySticker(
            $picture,
            $stickerPath,
            $sticker['x'],
            $sticker['y'],
            $sticker['w'],
            $sticker['h'],
            $sticker['angle']
        );
        if (!$success)
            return false;

        return imagejpeg($picture, $destPath, 90);
    }
}