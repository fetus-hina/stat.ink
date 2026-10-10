<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\components\db\Migration;

/**
 * Allows a user to forget their password when they have at least one passkey.
 *
 * The invariant "a user must have a password or at least one passkey" spans
 * multiple tables, so it is enforced by deferred constraint triggers rather
 * than a CHECK constraint.
 *
 * Deleting user_passkey_user cascades to user_passkey, so the trigger on
 * user_passkey also covers that path.
 */
final class m261010_072922_user_password_nullable extends Migration
{
    private const FUNCTION_NAME = 'check_user_has_credential';

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeUp()
    {
        $this->alterColumn('{{%user}}', 'password', 'DROP NOT NULL');

        // The row lock on "user" serializes concurrent transactions (e.g.,
        // deleting the last two passkeys at the same time). Each statement in
        // PL/pgSQL takes a new snapshot under READ COMMITTED, so the EXISTS
        // check after the lock sees the changes committed by the other one.
        $this->execute(
            'CREATE FUNCTION ' . self::FUNCTION_NAME . "() RETURNS TRIGGER\n" .
            <<<'SQL'
            LANGUAGE plpgsql
            AS $$
            DECLARE
                target_ids INTEGER[];
                target_id INTEGER;
            BEGIN
                IF TG_TABLE_NAME = 'user' THEN
                    target_ids := ARRAY[NEW.id];
                ELSIF TG_OP = 'DELETE' THEN
                    target_ids := ARRAY[OLD.user_id];
                ELSE
                    target_ids := ARRAY[OLD.user_id, NEW.user_id];
                END IF;

                FOREACH target_id IN ARRAY target_ids LOOP
                    PERFORM 1
                        FROM "user"
                        WHERE "id" = target_id AND "password" IS NULL
                        FOR UPDATE;
                    IF FOUND AND NOT EXISTS (
                        SELECT 1 FROM "user_passkey" WHERE "user_id" = target_id
                    ) THEN
                        RAISE EXCEPTION 'User % must have a password or at least one passkey', target_id
                            USING ERRCODE = 'check_violation';
                    END IF;
                END LOOP;

                RETURN NULL;
            END;
            $$
            SQL,
        );

        $this->execute(
            'CREATE CONSTRAINT TRIGGER [[user_has_credential]] ' .
            'AFTER INSERT OR UPDATE OF [[password]] ON {{%user}} ' .
            'DEFERRABLE INITIALLY DEFERRED ' .
            'FOR EACH ROW EXECUTE FUNCTION ' . self::FUNCTION_NAME . '()',
        );

        $this->execute(
            'CREATE CONSTRAINT TRIGGER [[user_has_credential]] ' .
            'AFTER DELETE OR UPDATE OF [[user_id]] ON {{%user_passkey}} ' .
            'DEFERRABLE INITIALLY DEFERRED ' .
            'FOR EACH ROW EXECUTE FUNCTION ' . self::FUNCTION_NAME . '()',
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeDown()
    {
        $this->execute('DROP TRIGGER [[user_has_credential]] ON {{%user_passkey}}');
        $this->execute('DROP TRIGGER [[user_has_credential]] ON {{%user}}');
        $this->execute('DROP FUNCTION ' . self::FUNCTION_NAME . '()');

        // This fails if any user has already forgotten their password.
        $this->alterColumn('{{%user}}', 'password', 'SET NOT NULL');

        return true;
    }
}
