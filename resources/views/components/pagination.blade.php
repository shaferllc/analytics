@if($data->hasPages())
    <div class="mt-12 bg-gradient-to-r from-blue-100 to-purple-100 dark:from-blue-900 dark:to-purple-900 p-6 rounded-xl shadow-lg">
        <nav aria-label="Pagination">
            {{ $data->onEachSide(2)->links() }}
        </nav>
    </div>
@endif