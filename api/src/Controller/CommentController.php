<?php

namespace App\Controller;

use App\Models\Comment;

class CommentController
{
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getTaskComments($taskId): array
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM task_comments WHERE task_id = :taskId');
            $stmt->bindParam(':taskId', $taskId);
            $stmt->execute();

            $commentsData = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $comments = [];
            foreach ($commentsData as $commentData) {
                $comment = new Comment(
                    $commentData['id'],
                    $commentData['task_id'],
                    $commentData['description'],
                    $commentData['image']
                );

                $commentArray = [
                    'id' => $comment->getId(),
                    'task_id' => $comment->getTaskId(),
                    'description' => $comment->getDescription(),
                    'image' => $comment->getImage(),
                ];

                $comments[] = $commentArray;
            }

            return ['comments' => $comments];
        } catch (\PDOException $e) {
            return ['error' => 'Error while getting task comments: ' . $e->getMessage()];
        }
    }

    public function createNewComment($taskId, $commentDescription, $commentImage)
    {
        try {
            $stmt = $this->pdo->prepare('
                INSERT INTO task_comments (task_id, description, image)
                VALUES (:task_id, :description, :image);
            ');

            $stmt->execute([
                ':task_id' => $taskId,
                ':description' => $commentDescription,
                ':image' => $commentImage,
            ]);

            return ['message' => 'New Comment Succefully Created'];
        } catch (\PDOException $e) {
            return ['error' => 'Error while creating comment: ' . $e->getMessage()];
        }
    }

    public function updateCommentObject(Comment $comment)
    {
        try {
            $stmt = $this->pdo->prepare('
                UPDATE task_comments 
                SET task_id = :task_id, description = :description, image = :image
                WHERE id = :commentId;
            ');

            $stmt->execute([
                ':task_id' => $comment->getTaskId(),
                ':description' => $comment->getDescription(),
                ':image' => $comment->getImage(),
                ':commentId' => $comment->getId(),
            ]);

            return ['message' => 'Comentário atualizado com sucesso'];
        } catch (\PDOException $e) {
            return ['error' => 'Erro ao atualizar o comentário: ' . $e->getMessage()];
        }
    }

    public function deleteComment($commentId)
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM task_comments WHERE id = :commentId');
            $stmt->bindParam(':commentId', $commentId);
            $stmt->execute();

            return ['message' => 'Comentário excluído com sucesso'];
        } catch (\PDOException $e) {
            return ['error' => 'Erro ao excluir o comentário: ' . $e->getMessage()];
        }
    }

    public function getCommentById($commentId): array
    {
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM task_comments WHERE id = :commentId');
            $stmt->bindParam(':commentId', $commentId);
            $stmt->execute();

            $commentData = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$commentData) {
                return ['error' => 'Comment not found'];
            }

            $comment = new Comment(
                $commentData['id'],
                $commentData['task_id'],
                $commentData['description'],
                $commentData['image']
            );

            $commentArray = [
                'id' => $comment->getId(),
                'task_id' => $comment->getTaskId(),
                'description' => $comment->getDescription(),
                'image' => $comment->getImage(),
            ];

            return ['comment' => $commentArray];
        } catch (\PDOException $e) {
            return ['error' => 'Error while getting comment: ' . $e->getMessage()];
        }
    }

}
