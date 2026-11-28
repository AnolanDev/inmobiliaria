<?php

namespace App\Console\Commands;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Validation\ValidationException;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

/**
 * Creates application users. Public registration is disabled, so this is the
 * only way to add users until an admin module exists.
 *
 * The password is only ever typed at a hidden prompt: there is deliberately
 * no --password option (it would end up in the shell history), and it is
 * never printed or logged.
 */
#[Signature('users:create')]
#[Description('Create a user interactively; the password is typed hidden and never shown or logged')]
class CreateUser extends Command
{
    public function handle(CreateNewUser $creator): int
    {
        $name = text(label: __('Name'), required: true);
        $email = text(label: __('Email'), required: true);
        $password = password(label: __('Password'), required: true);
        $passwordConfirmation = password(label: __('Confirm password'), required: true);

        try {
            $user = $creator->create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'password_confirmation' => $passwordConfirmation,
            ]);
        } catch (ValidationException $exception) {
            foreach ($exception->validator->errors()->all() as $message) {
                $this->components->error($message);
            }

            return self::FAILURE;
        } finally {
            unset($password, $passwordConfirmation);
        }

        // An administrator vouches for the address, so no verification e-mail
        // is needed before the first login.
        $user->forceFill(['email_verified_at' => now()])->save();

        $this->components->info(__('User created: :email', ['email' => $user->email]));

        return self::SUCCESS;
    }
}
