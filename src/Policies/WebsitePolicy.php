<?php

namespace ShaferLLC\Analytics\Policies;

use ShaferLLC\Analytics\Models\User;
use ShaferLLC\Analytics\Models\Website;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class WebsitePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  User  $user
     * @return Response|bool
     */
    public function viewAny(User $user): Response|bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  User  $user
     * @param  Website  $website
     * @return Response|bool
     */
    public function view(User $user, Website $website): Response|bool
    {
        return $user->id === $website->user_id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  User  $user
     * @return Response|bool
     */
    public function create(User $user): Response|bool
    {
        $websiteLimit = $user->plan->features->websites;
        
        if ($websiteLimit === -1) {
            return true;
        }
        
        return $user->websites()->count() < $websiteLimit;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param  Website  $website
     * @return Response|bool
     */
    public function update(User $user, Website $website): Response|bool
    {
        return $user->id === $website->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param  Website  $website
     * @return Response|bool
     */
    public function delete(User $user, Website $website): Response|bool
    {
        return $user->id === $website->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param  Website  $website
     * @return Response|bool
     */
    public function restore(User $user, Website $website): Response|bool
    {
        return $user->id === $website->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @param  Website  $website
     * @return Response|bool
     */
    public function forceDelete(User $user, Website $website): Response|bool
    {
        return $user->id === $website->user_id;
    }
}
