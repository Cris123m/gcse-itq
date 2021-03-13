<?php

namespace App\Actions\Fortify;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'identification' => ['required', 'integer', 'min:0100000000' ,'max:9999999999', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'institutional_email' => [ 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['string','min:7','max:9'],
            'cellphone' => ['string','min:10','max:10'],
            'address' => ['string'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
        ])->validate();

        return DB::transaction(function () use ($input) {
            return tap(User::create([
                'identification' => $input['identification'],
                'name' => $input['name'],
                'email' => $input['email'],
                'institutional_email' => $input['institutional_email'],
                'phone' => $input['phone'],
                'cellphone' => $input['cellphone'],
                'address' => $input['address'],
                'password' => Hash::make($input['password']),
            ]), function (User $user) {
                $this->createTeam($user);
            });
        });
    }

    /**
     * Create a personal team for the user.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    protected function createTeam(User $user)
    {
        $user->ownedTeams()->save(Team::forceCreate([
            'user_id' => $user->id,
            'name' => "Equipo de ".explode(' ', $user->name, 2)[0],
            'personal_team' => true,
        ]));
    }
}
