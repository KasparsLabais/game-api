<?php

namespace PartyGames\GameApi;

/**
 * Host subscription tiers. A user's tier comes from users.premium_level
 * (0 / 10 / 20 / 30, set by the subscriptions package on checkout), mapped
 * to tier 0..3. Tier 0 is the free default.
 *
 * This is the single source of truth for what each plan may do — edit the
 * matrix here to re-balance limits.
 */
class Tiers
{
    public const TIERS = [
        0 => [
            'name'           => 'Free',
            'max_players'    => 10,
            'max_questions'  => 15,
            'premium_access' => false,
            'question_types' => ['options'],
            'seasons'        => false,
            'events'         => false,
        ],
        1 => [
            'name'           => 'Beginner',
            'max_players'    => 20,
            'max_questions'  => 25,
            'premium_access' => true,
            'question_types' => ['options', 'text_input'],
            'seasons'        => false,
            'events'         => false,
        ],
        2 => [
            'name'           => 'Advanced',
            'max_players'    => 35,
            'max_questions'  => 50,
            'premium_access' => true,
            'question_types' => ['options', 'text_input'],
            'seasons'        => false,
            'events'         => false,
        ],
        3 => [
            'name'           => 'Pro',
            'max_players'    => null,   // unlimited
            'max_questions'  => null,   // unlimited
            'premium_access' => true,
            'question_types' => ['options', 'text_input', 'open_input'],
            'seasons'        => true,
            'events'         => true,
        ],
    ];

    /** Map a raw premium_level ('none' / '10' / '20' / '30') to a tier index. */
    public static function levelToTier($premiumLevel): int
    {
        $n = is_numeric($premiumLevel) ? (int) $premiumLevel : 0;
        return max(0, min(3, intdiv($n, 10)));
    }

    /** The tier index (0..3) for a user (0 when null / guest). */
    public static function tierIndex($user): int
    {
        return $user ? self::levelToTier($user->premium_level ?? 0) : 0;
    }

    /** The full capability array for a user, plus a 'tier' key. */
    public static function forUser($user): array
    {
        $tier = self::tierIndex($user);
        return self::TIERS[$tier] + ['tier' => $tier];
    }

    /** A single capability value for a user (e.g. 'max_questions'). */
    public static function limit($user, string $key)
    {
        return self::forUser($user)[$key] ?? null;
    }

    /** True when a boolean capability is granted to the user. */
    public static function allows($user, string $key): bool
    {
        return (bool) (self::forUser($user)[$key] ?? false);
    }

    /** Human label for a user's current plan. */
    public static function name($user): string
    {
        return self::forUser($user)['name'];
    }
}
