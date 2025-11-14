<?php

namespace App\Livewire\Tenant\Websites;

use App\Actions\Tenant\Websites\CreateWebsiteAction;
use App\Actions\Tenant\Websites\UpdateWebsiteAction;
use App\Data\Tenant\Websites\CreateWebsiteData;
use App\Data\Tenant\Websites\UpdateWebsiteData;
use App\Enums\WebsiteEnvironment;
use App\Enums\WebsiteStatus;
use App\Models\Tenant\Client;
use App\Models\Tenant\Project;
use App\Models\Tenant\Website;
use Livewire\Attributes\Validate;
use Livewire\Component;

class WebsiteForm extends Component
{
    public ?string $websiteId = null;

    #[Validate('required|uuid|exists:clients,id')]
    public string $client_id = '';

    #[Validate('nullable|uuid|exists:projects,id')]
    public ?string $project_id = null;

    #[Validate('required|min:2|max:255')]
    public string $name = '';

    #[Validate('required|url|max:255')]
    public string $url = '';

    #[Validate('nullable')]
    public ?string $environment = null;

    #[Validate('nullable')]
    public ?string $status = null;

    #[Validate('nullable|max:50')]
    public ?string $server_provider = null;

    #[Validate('nullable|max:255')]
    public ?string $server_id = null;

    #[Validate('nullable|max:255')]
    public ?string $server_ip = null;

    #[Validate('nullable|max:50')]
    public ?string $repository_provider = null;

    #[Validate('nullable|url|max:255')]
    public ?string $repository_url = null;

    #[Validate('nullable|max:100')]
    public ?string $repository_branch = 'main';

    #[Validate('nullable|max:50')]
    public ?string $deployment_method = null;

    #[Validate('nullable|max:5000')]
    public ?string $notes = null;

    public function mount(?string $websiteId = null): void
    {
        $this->websiteId = $websiteId;

        if ($websiteId) {
            $website = Website::findOrFail($websiteId);

            $this->client_id = $website->client_id;
            $this->project_id = $website->project_id;
            $this->name = $website->name;
            $this->url = $website->url;
            $this->environment = $website->environment->value;
            $this->status = $website->status->value;
            $this->server_provider = $website->server_provider;
            $this->server_id = $website->server_id;
            $this->server_ip = $website->server_ip;
            $this->repository_provider = $website->repository_provider;
            $this->repository_url = $website->repository_url;
            $this->repository_branch = $website->repository_branch;
            $this->deployment_method = $website->deployment_method;
            $this->notes = $website->notes;
        }
    }

    public function save(): void
    {
        $this->validate();

        try {
            if ($this->websiteId) {
                // Update existing website
                $website = Website::findOrFail($this->websiteId);
                $data = UpdateWebsiteData::from([
                    'name' => $this->name,
                    'url' => $this->url,
                    'environment' => $this->environment ? WebsiteEnvironment::from($this->environment) : null,
                    'status' => $this->status ? WebsiteStatus::from($this->status) : null,
                    'server_provider' => $this->server_provider,
                    'server_id' => $this->server_id,
                    'server_ip' => $this->server_ip,
                    'repository_provider' => $this->repository_provider,
                    'repository_url' => $this->repository_url,
                    'repository_branch' => $this->repository_branch,
                    'deployment_method' => $this->deployment_method,
                    'notes' => $this->notes,
                ]);

                $action = new UpdateWebsiteAction();
                $action($website, $data);

                session()->flash('success', 'Website updated successfully.');
            } else {
                // Create new website
                $data = CreateWebsiteData::from([
                    'client_id' => $this->client_id,
                    'project_id' => $this->project_id,
                    'name' => $this->name,
                    'url' => $this->url,
                    'environment' => $this->environment ? WebsiteEnvironment::from($this->environment) : null,
                    'status' => $this->status ? WebsiteStatus::from($this->status) : null,
                    'server_provider' => $this->server_provider,
                    'server_id' => $this->server_id,
                    'server_ip' => $this->server_ip,
                    'repository_provider' => $this->repository_provider,
                    'repository_url' => $this->repository_url,
                    'repository_branch' => $this->repository_branch,
                    'deployment_method' => $this->deployment_method,
                    'notes' => $this->notes,
                ]);

                $action = new CreateWebsiteAction();
                $action($data);

                session()->flash('success', 'Website created successfully.');
            }

            $this->redirect(route('websites.index'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $clients = Client::whereNull('archived_at')
            ->orderBy('name')
            ->get();

        $projects = collect();
        if ($this->client_id) {
            $projects = Project::where('client_id', $this->client_id)
                ->whereNull('archived_at')
                ->orderBy('name')
                ->get();
        }

        return view('livewire.tenant.websites.website-form', [
            'clients' => $clients,
            'projects' => $projects,
        ])->layout('layouts.tenant', [
            'header' => $this->websiteId ? 'Edit Website' : 'Create Website'
        ]);
    }
}
