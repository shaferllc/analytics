@if($paginator->hasPages())
    <div class="mt-12 bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl shadow-lg border border-gray-700 p-6">
        <nav aria-label="Pagination" >
            {{ $paginator->onEachSide(2)->links() }}
        </nav>
    </div>
@endif
