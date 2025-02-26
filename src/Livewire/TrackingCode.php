<?php

namespace Shaferllc\Analytics\Livewire;

use App\Models\Site;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Shaferllc\Analytics\Traits\ComponentTrait;
use Shaferllc\Analytics\Traits\DateRangeTrait;

#[Title('Tracking Code')]
class TrackingCode extends Component
{
    use DateRangeTrait, ComponentTrait;

    #[Locked]
    public Site $site;
    public bool $copied = false;
    public ?string $verificationStatus = null;


    public function copyToClipboard()
    {
        try {
            $this->copied = false;
            $this->dispatchBrowserEvent('copy-to-clipboard', ['id' => 'i-tracking-code']);
            $this->copied = true;
        } catch (\Exception $e) {
            $this->addError('copy', __('Failed to copy to clipboard'));
        }
    }

    public function verifyInstallation()
    {
        $this->verificationStatus = $this->checkTrackingCodeInstallation();
    }

    protected function checkTrackingCodeInstallation(): string
    {
        // Implementation of tracking code verification
        return 'verified'; // or 'not_verified'
    }

    public function render()
    {
        return view('analytics::livewire.tracking-code');
    }
}
