
<x-filament-panels::page>

    {{ $this->content }}

    <x-filament::modal id="generate-request" width="3xl">
        <x-slot name="heading">
            Generate GiveCampus Request
        </x-slot>
        <div>
            <form wire:submit.prevent="generateRequestId">
                {{  $this->generateRequestForm }}

                <div class="flex justify-center mt-8">
                    <x-filament::button type="submit">Generate</x-filament::button>
                </div>
                @if ($api_request_id)
                <div class="mt-8">
                    <div class="border-dashed bg-neutral-500/10 p-4 rounded-md border-2 border-neutral-300">
                        <p>Request ID:</p>
                        <code class="font-bold text-2xl" id="api_request_id">{{ $api_request_id }}</code>
                    </div>
                </div>

                <div class="flex justify-center mt-8">
                    <x-filament::button
                        type="button"
                        icon="heroicon-o-clipboard"
                        x-on:click="
                            navigator.clipboard.writeText(
                                document.getElementById('api_request_id').textContent.trim()
                            ).then(() => {
                                $tooltip('Copied!', { timeout: 2000 })
                            })
                        "
                    >
                        Copy Request ID
                    </x-filament::button>
                </div>
            @endif
            </form>
        </div>
    </x-filament::modal>

    <x-filament::modal id="givecampus" width="5xl">
        <x-slot name="heading">
            Fetch GiveCampus
        </x-slot>

        <div
            class="space-y-8"
            x-data="{
                showSection2: false,
                showSection3: false,
                isSyncing: false,
                canSync: false,

                async syncGiveCampus() {
                    if (this.isSyncing) {
                        return;
                    }

                    this.isSyncing = true;
                    this.showSection2 = false;
                    this.showSection3 = false;

                    try {
                        // #1: Request gifts
                        await $wire.getGifts();

                        this.showSection2 = true;

                        // #2: Poll request status
                        let attempts = 0;
                        const maxAttempts = 60;
                        let completed = false;

                        while (attempts < maxAttempts) {
                            await new Promise(resolve =>
                                setTimeout(resolve, 3000)
                            );

                            const result = await $wire.getRequest();

                            attempts++;

                            if (result?.status === 'completed') {
                                completed = true;
                                break;
                            }

                            if (
                                result?.status === 'failed' ||
                                result?.status === 'error'
                            ) {
                                throw new Error(
                                    'GiveCampus request failed.'
                                );
                            }
                        }

                        if (!completed) {
                            throw new Error(
                                'Timed out waiting for GiveCampus.'
                            );
                        }

                        // #3: Download results
                        this.showSection3 = true;

                        await $wire.getDownload();

                        this.canSync = true;

                    } catch (error) {
                        console.error(
                            'GiveCampus sync failed:',
                            error
                        );

                        alert(
                            error.message ||
                            'An error occurred while syncing GiveCampus.'
                        );
                    } finally {
                        this.isSyncing = false;
                    }
                }
            }"
        >

            {{-- #1: GET /gifts --}}
            <div class="space-y-4">
                <div class="bg-gray-300 px-3 py-1 dark:bg-gray-700">
                    1. <code>/gifts</code>
                </div>

                <div class="rounded-lg border p-4 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-medium">
                            Request gifts from GiveCampus
                        </span>

                        <span
                            @class([
                                'text-sm font-medium',
                                'text-success-600' => $status_gift === 'completed',
                                'text-danger-600' => $status_gift === 'failed',
                                'text-gray-500' => ! in_array(
                                    $status_gift,
                                    ['completed', 'failed']
                                ),
                            ])
                        >
                            {{ $status_gift ?? 'pending' }}
                        </span>
                    </div>

                    <div class="text-sm text-gray-500">
                        Sends the selected date range to the GiveCampus API.
                    </div>

                    <div>
                        <span class="font-medium">Request ID:</span>

                        <code class="break-all">
                            {{ $request_id ?? 'Waiting for request...' }}
                        </code>
                    </div>
                </div>
            </div>

            {{-- #2: GET /results/{request_id} --}}
            <div
                :class="showSection2 ? 'visible' : 'invisible'"
                class="space-y-4"
            >
                <div class="bg-gray-300 px-3 py-1 dark:bg-gray-700">
                    2. <code>/results/{request_id}</code>
                </div>

                <div class="rounded-lg border p-4 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-medium">
                            Check GiveCampus request status
                        </span>

                        <span
                            @class([
                                'text-sm font-medium',
                                'text-success-600' => $status_request === 'completed',
                                'text-danger-600' => $status_request === 'failed',
                                'text-primary-600' => $status_request === 'in_progress',
                                'text-gray-500' => ! in_array(
                                    $status_request,
                                    ['completed', 'failed', 'in_progress']
                                ),
                            ])
                        >
                            {{ $status_request ?? 'pending' }}
                        </span>
                    </div>

                    <div class="text-sm text-gray-500">
                        Polls the request until GiveCampus finishes processing.
                    </div>

                    <div>
                        <span class="font-medium">Request ID:</span>

                        <code class="break-all">
                            {{ $request_id ?? 'N/A' }}
                        </code>
                    </div>
                </div>
            </div>

            {{-- #3: Download URL --}}
            <div
                :class="showSection3 ? 'visible' : 'invisible'"
                class="space-y-4"
            >
                <div class="bg-gray-300 px-3 py-1 dark:bg-gray-700">
                    3. <code>{download_url}</code>
                </div>

                <div class="rounded-lg border p-4 space-y-2">

                    <div>
                        <span class="font-medium">Download URL:</span>

                        <code class="break-all">
                            @if($download_url)
                                <a
                                    href="{{ $download_url }}"
                                    target="_blank"
                                    class="text-primary-600 hover:underline"
                                >
                                    {{ $download_url }}
                                </a>
                            @endif
                        </code>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-3">
                <x-filament::button
                    color="gray"
                    outlined
                    x-on:click="$dispatch('close-modal', { id: 'givecampus' })"
                    type="button"
                >
                    Close
                </x-filament::button>

                <x-filament::button
                    color="primary"
                    x-on:click="syncGiveCampus()"
                    type="button"
                >
                    <span>
                        Import Now
                    </span>

                </x-filament::button>

                <x-filament::button
                    color="primary"
                    wire:click="syncData()"
                    type="button"
                    x-show="canSync"
                >
                    <span>
                        Sync Data
                    </span>

                </x-filament::button>
            </div>

        </div>
    </x-filament::modal>

</x-filament-panels::page>
