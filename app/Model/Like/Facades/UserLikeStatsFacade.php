<?php

namespace App\Model\Like\Facades;

use App\Model\Comment\Repo\CommentRepository;
use App\Model\Like\Repo\CommentLikeRepository;
use App\Model\Like\Repo\PostLikeRepository;
use App\Model\Post\Repo\PostRepository;
use Nette\Database\Explorer;

/**
 * Statistiky liků pro uživatele — kolik liků dostaly jeho posty a komentáře.
 */
final class UserLikeStatsFacade
{
    public function __construct(
        private readonly Explorer $database,
    ) {
    }

    /**
     * Vrátí celkový počet liků na postech daného uživatele.
     */
    public function getPostLikesCountByUser(int $userId): int
    {
        $row = $this->database->query(
            'SELECT COUNT(*) AS cnt FROM ' . PostLikeRepository::TABLE_NAME . ' pl ' .
            'JOIN ' . PostRepository::TABLE_NAME . ' p ON p.' . PostRepository::ID_COL . ' = pl.' . PostLikeRepository::POST_ID_COL . ' ' .
            'WHERE p.' . PostRepository::OWNER_COL . ' = ?', $userId
        )->fetch();

        return $row ? (int) $row->cnt : 0;
    }

    /**
     * Vrátí celkový počet liků na komentářích daného uživatele.
     */
    public function getCommentLikesCountByUser(int $userId): int
    {
        $row = $this->database->query(
            'SELECT COUNT(*) AS cnt FROM ' . CommentLikeRepository::TABLE_NAME . ' cl ' .
            'JOIN ' . CommentRepository::TABLE_NAME . ' c ON c.' . CommentRepository::ID_COL . ' = cl.' . CommentLikeRepository::COMMENT_ID_COL . ' ' .
            'WHERE c.' . CommentRepository::OWNER_COL . ' = ?', $userId
        )->fetch();

        return $row ? (int) $row->cnt : 0;
    }

    /**
     * Vrátí statistiky liků pro všechny uživatele najednou (efektivní pro seznam).
     * @return array<int, array{postLikes: int, commentLikes: int}> Klíč = userId
     */
    public function getAllUsersLikeStats(): array
    {
        $stats = [];

        // Post likes per user
        $postLikes = $this->database->query(
            'SELECT p.' . PostRepository::OWNER_COL . ' AS user_id, COUNT(*) AS cnt ' .
            'FROM ' . PostLikeRepository::TABLE_NAME . ' pl ' .
            'JOIN ' . PostRepository::TABLE_NAME . ' p ON p.' . PostRepository::ID_COL . ' = pl.' . PostLikeRepository::POST_ID_COL . ' ' .
            'GROUP BY p.' . PostRepository::OWNER_COL
        );

        foreach ($postLikes as $row) {
            $stats[(int) $row->user_id]['postLikes'] = (int) $row->cnt;
        }

        // Comment likes per user
        $commentLikes = $this->database->query(
            'SELECT c.' . CommentRepository::OWNER_COL . ' AS user_id, COUNT(*) AS cnt ' .
            'FROM ' . CommentLikeRepository::TABLE_NAME . ' cl ' .
            'JOIN ' . CommentRepository::TABLE_NAME . ' c ON c.' . CommentRepository::ID_COL . ' = cl.' . CommentLikeRepository::COMMENT_ID_COL . ' ' .
            'GROUP BY c.' . CommentRepository::OWNER_COL
        );

        foreach ($commentLikes as $row) {
            $uid = (int) $row->user_id;
            if (!isset($stats[$uid])) {
                $stats[$uid] = ['postLikes' => 0];
            }
            $stats[$uid]['commentLikes'] = (int) $row->cnt;
        }

        // Doplnit nuly kde chybí
        foreach ($stats as &$s) {
            $s += ['postLikes' => 0, 'commentLikes' => 0];
        }

        return $stats;
    }
}

