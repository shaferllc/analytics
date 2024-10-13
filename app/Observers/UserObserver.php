<?php

namespace ShaferLLC\Analytics\Observers;

use ShaferLLC\Analytics\Models\User;
use ShaferLLC\Analytics\Traits\WebhookTrait;

class UserObserver
{
    use WebhookTrait;

    /**
     * Handle the User "created" event.
     *
     * @param  \ShaferLLC\Analytics\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        $this->callWebhook(config('settings.webhook_user_created'), $this->getUserData($user, 'created'));
    }

    /**
     * Handle the User "updated" event.
     *
     * @param  \ShaferLLC\Analytics\Models\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        $this->callWebhook(config('settings.webhook_user_updated'), $this->getUserData($user, 'updated'));
    }

    /**
     * Handle the User "forceDeleted" event.
     *
     * @param  \ShaferLLC\Analytics\Models\User  $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        $this->callWebhook(config('settings.webhook_user_deleted'), $this->getUserData($user, 'deleted'));
    }

    /**
     * Handle the User "deleting" event.
     *
     * @param  \ShaferLLC\Analytics\Models\User  $user
     * @return void
     */
    public function deleting(User $user)
    {
        if ($user->isForceDeleting()) {
            $user->stats()->delete();
            $user->recents()->delete();
            $user->websites()->delete();
        }

        $this->cancelSubscription($user);
    }

    /**
     * Get user data for webhook.
     *
     * @param  \ShaferLLC\Analytics\Models\User  $user
     * @param  string  $action
     * @return array
     */
    private function getUserData(User $user, string $action): array
    {
        return [
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'action' => $action
        ];
    }

    /**
     * Cancel user subscription if exists.
     *
     * @param  \ShaferLLC\Analytics\Models\User  $user
     * @return void
     */
    private function cancelSubscription(User $user): void
    {
        if ($user->plan_subscription_id) {
            $user->planSubscriptionCancel();
        }
    }
}
