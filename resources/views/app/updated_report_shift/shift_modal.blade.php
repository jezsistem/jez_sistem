<!-- User Shift Detail Modal -->
<div id="UserShiftModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 overflow-y-auto" style="padding: 1rem;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-6xl w-full mx-auto my-8" style="max-height: 90vh; overflow-y: auto;">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-5 border-b border-blue-700 flex justify-between items-center rounded-t-2xl">
            <h5 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-clock mr-3"></i>
                Data User Shift
            </h5>
            <button type="button" class="text-white hover:text-gray-200 transition-colors p-2 hover:bg-white hover:bg-opacity-20 rounded-lg" id="close_user_shift_btn">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6 bg-gray-50">
            <div id="UserShiftModalBody"></div>
        </div>
        <div class="bg-gray-100 px-6 py-4 border-t border-gray-200 flex justify-end rounded-b-2xl">
            <button type="button" class="px-6 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-red-600 transition-colors shadow-md" id="close_user_shift_btn_2">
                <i class="fas fa-times mr-2"></i>Tutup
            </button>
        </div>
    </div>
</div>

<!-- Product Sold Modal -->
<div id="UserShiftDetailSoldModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 overflow-y-auto" style="padding: 1rem;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full mx-auto my-8" style="max-height: 90vh; overflow-y: auto;">
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-5 border-b border-green-700 flex justify-between items-center rounded-t-2xl">
            <h5 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-shopping-cart mr-3"></i>
                Detail Produk Terjual
            </h5>
            <button type="button" class="text-white hover:text-gray-200 transition-colors p-2 hover:bg-white hover:bg-opacity-20 rounded-lg" id="close_user_shift_detail_sold_btn">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6 bg-gray-50">
            <div id="UserShiftDetailSoldModalBody"></div>
        </div>
        <div class="bg-gray-100 px-6 py-4 border-t border-gray-200 flex justify-end rounded-b-2xl">
            <button type="button" class="px-6 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors shadow-md" id="close_user_shift_detail_sold_btn_2">
                <i class="fas fa-times mr-2"></i>Tutup
            </button>
        </div>
    </div>
</div>

<!-- Product Refund Modal -->
<div id="UserShiftDetailRefundModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 overflow-y-auto" style="padding: 1rem;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full mx-auto my-8" style="max-height: 90vh; overflow-y: auto;">
        <div class="bg-gradient-to-r from-red-500 to-rose-600 px-6 py-5 border-b border-red-700 flex justify-between items-center rounded-t-2xl">
            <h5 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-undo mr-3"></i>
                Detail Produk Refund
            </h5>
            <button type="button" class="text-white hover:text-gray-200 transition-colors p-2 hover:bg-white hover:bg-opacity-20 rounded-lg" id="close_user_shift_detail_refund_btn">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6 bg-gray-50">
            <div id="UserShiftDetailRefundModalBody"></div>
        </div>
        <div class="bg-gray-100 px-6 py-4 border-t border-gray-200 flex justify-end rounded-b-2xl">
            <button type="button" class="px-6 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-700 transition-colors shadow-md" id="close_user_shift_detail_refund_btn_2">
                <i class="fas fa-times mr-2"></i>Tutup
            </button>
        </div>
    </div>
</div>
