@props(['class' => '', 'tabs' => false])

<div {{ $attributes->merge(['class' => 'flex flex-col space-y-1.5 p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700' . $class]) }}>
    <!-- Contenu principal -->
    <div class="flex items-center justify-between">
        <div>
            {{ $slot }}
        </div>
        
        @if(isset($actions))
            <div class="flex items-center space-x-2">
                {{ $actions }}
            </div>
        @endif
    </div>
    
    <!-- Onglets conditionnels -->
    @if($tabs && isset($tabsContent))
        <div class="pt-4 -mb-4">
            {{ $tabsContent }}
        </div>
    @endif
</div>