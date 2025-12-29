<template>
  <AppLayout>
    <!-- Header - Hidden on Print -->
    <div class="mb-3 mt-2 flex flex-col md:flex-row justify-between md:items-center gap-2 no-print">
      <div>
        <h1 class="text-xl font-bold text-gray-800">
          Payment Receipt
        </h1>
        <p class="text-xs text-gray-500 mt-0.5">View payment details and receipt</p>
      </div>
      
      <div class="flex items-center gap-2">
        <button 
          @click="printReceipt"
          class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-600 hover:bg-green-700 rounded-lg text-white text-sm font-medium transition-colors"
        >
          <i class="fas fa-print text-xs"></i>
          Print Receipt
        </button>
        
        <router-link 
          to="/payments"
          class="inline-flex items-center gap-1 px-3 py-1.5 border bg-blue-600 hover:bg-blue-700 rounded-lg text-gray-100 text-sm font-medium transition-colors"
        >
          <i class="fas fa-arrow-left text-xs"></i>
          Back to List
        </router-link>
      </div>
    </div>

    <!-- Loading State - Hidden on Print -->
    <div v-if="loading" class="bg-white rounded-xl shadow border border-gray-100 p-8 text-center no-print">
      <i class="fas fa-spinner fa-spin text-3xl text-blue-600 mb-3"></i>
      <p class="text-gray-600">Loading payment details...</p>
    </div>

    <!-- Receipt Container -->
    <div v-if="!loading && payment" class="max-w-4xl mx-auto no-print-margin">
      <!-- Receipt Card -->
      <div id="receipt-content" class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <!-- Receipt Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-3 print:p-2">
          <div class="text-center">
            <div class="mb-1.5 print:mb-1">
              <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/20 backdrop-blur-sm print:w-8 print:h-8">
                <i class="fas fa-receipt text-lg print:text-base"></i>
              </div>
            </div>
            <h2 class="text-lg font-bold mb-0.5 print:text-base">Payment Receipt</h2>
            <p class="text-blue-100 text-xs print:text-[10px]">Official Payment Confirmation</p>
            <div class="mt-2 pt-2 border-t border-white/20 print:mt-1.5 print:pt-1.5">
              <div class="text-[10px] text-blue-100">Receipt No.</div>
              <div class="text-base font-bold print:text-sm">#{{ payment.id }}</div>
            </div>
          </div>
        </div>

        <!-- Receipt Body -->
        <div class="p-3 print:p-2">
          <!-- Payment Date -->
          <div class="mb-3 text-center print:mb-1.5">
            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 rounded-full print:px-2 print:py-0.5">
              <i class="fas fa-calendar text-gray-600 text-[10px]"></i>
              <span class="text-[11px] font-semibold text-gray-700 print:text-[10px]">
                Payment Date: {{ formatDate(payment.payment_date) }}
              </span>
            </div>
          </div>

          <!-- Student Information -->
          <div class="mb-3 print:mb-2">
            <div class="flex items-center gap-1.5 mb-1.5 print:mb-1">
              <div class="p-1 bg-blue-100 rounded-lg">
                <i class="fas fa-user-graduate text-blue-600 text-xs print:text-[10px]"></i>
              </div>
              <h3 class="text-sm font-bold text-gray-800 print:text-xs">Student Information</h3>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-2.5 border border-gray-200 print:p-1.5">
              <div class="flex items-start gap-3 print:gap-2">
                <!-- Student Image -->
                <div class="shrink-0">
                  <div class="w-14 h-14 rounded-full overflow-hidden border-2 border-white shadow-md print:w-10 print:h-10">
                    <img 
                      v-if="payment.class_wise_student?.student?.student_image_url"
                      :src="payment.class_wise_student.student.student_image_url" 
                      :alt="payment.class_wise_student?.student?.student_name"
                      class="w-full h-full object-cover"
                    >
                    <div v-else class="w-full h-full bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center">
                      <i class="fas fa-user text-blue-400 text-lg print:text-xs"></i>
                    </div>
                  </div>
                </div>

                <!-- Student Details -->
                <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-2 print:gap-1.5">
                  <div>
                    <div class="text-[10px] text-gray-500 mb-0.5">Student Name</div>
                    <div class="font-semibold text-gray-800 text-xs print:text-[11px]">
                      {{ payment.class_wise_student?.student?.student_name || 'N/A' }}
                    </div>
                  </div>
                  
                  <div>
                    <div class="text-[10px] text-gray-500 mb-0.5">ID Card No.</div>
                    <div class="font-semibold text-gray-800 text-xs print:text-[11px]">
                      {{ payment.class_wise_student?.student?.id_card_number || 'N/A' }}
                    </div>
                  </div>
                  
                  <div>
                    <div class="text-[10px] text-gray-500 mb-0.5">Class Roll</div>
                    <div>
                      <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-blue-100 text-blue-800">
                        {{ payment.class_wise_student?.class_roll || 'N/A' }}
                      </span>
                    </div>
                  </div>
                  
                  <div>
                    <div class="text-[10px] text-gray-500 mb-0.5">Mobile Number</div>
                    <div class="font-semibold text-gray-800 text-xs print:text-[11px]">
                      {{ payment.class_wise_student?.student?.mobile_number || 'N/A' }}
                    </div>
                  </div>
                </div>
              </div>

              <!-- Academic Info -->
              <div class="mt-2.5 pt-2.5 border-t border-gray-200 print:mt-1.5 print:pt-1.5">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 print:gap-1.5">
                  <div class="text-center">
                    <div class="text-[10px] text-gray-500 mb-0.5">Session</div>
                    <div class="font-semibold text-gray-800 text-xs print:text-[11px]">
                      {{ payment.class_wise_student?.session?.session_name || 'N/A' }}
                    </div>
                  </div>
                  <div class="text-center">
                    <div class="text-[10px] text-gray-500 mb-0.5">Class</div>
                    <div class="font-semibold text-gray-800 text-xs print:text-[11px]">
                      {{ payment.class_wise_student?.class?.class_name || 'N/A' }}
                    </div>
                  </div>
                  <div class="text-center">
                    <div class="text-[10px] text-gray-500 mb-0.5">Version</div>
                    <div class="font-semibold text-gray-800 text-xs print:text-[11px]">
                      {{ payment.class_wise_student?.version?.version_name || 'N/A' }}
                    </div>
                  </div>
                  <div class="text-center">
                    <div class="text-[10px] text-gray-500 mb-0.5">Section</div>
                    <div class="font-semibold text-gray-800 text-xs print:text-[11px]">
                      {{ payment.class_wise_student?.section?.section_name || 'N/A' }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Payment Details -->
          <div class="mb-3 print:mb-2">
            <div class="flex items-center gap-1.5 mb-1.5 print:mb-1">
              <div class="p-1 bg-green-100 rounded-lg">
                <i class="fas fa-money-bill-wave text-green-600 text-xs print:text-[10px]"></i>
              </div>
              <h3 class="text-sm font-bold text-gray-800 print:text-xs">Payment Details</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-2.5 print:gap-1.5 print:mb-1.5">
              <!-- Month -->
              <div class="bg-indigo-50 rounded-lg p-2 border border-indigo-200 print:p-1.5">
                <div class="flex items-center gap-1.5">
                  <i class="fas fa-calendar-day text-indigo-600 text-xs print:text-[10px]"></i>
                  <div class="flex-1">
                    <div class="text-[10px] text-indigo-600 mb-0.5">Payment For</div>
                    <div class="font-semibold text-gray-800 text-xs print:text-[11px]">
                      {{ payment.month?.month_name || 'N/A' }}
                    </div>
                  </div>
                </div>
              </div>

              <!-- Account -->
              <div class="bg-blue-50 rounded-lg p-2 border border-blue-200 print:p-1.5">
                <div class="flex items-center gap-1.5">
                  <i :class="payment.account?.account_type === 'bank' ? 'fas fa-university' : 'fas fa-wallet'" class="text-blue-600 text-xs print:text-[10px]"></i>
                  <div class="flex-1">
                    <div class="text-[10px] text-blue-600 mb-0.5">Paid To</div>
                    <div class="font-semibold text-gray-800 text-xs print:text-[11px]">
                      {{ payment.account?.account_name || 'N/A' }}
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Fee Details Table -->
            <div class="overflow-hidden rounded-lg border border-gray-200">
              <table class="w-full">
                <thead class="bg-gray-100">
                  <tr>
                    <th class="px-2 py-1.5 text-left text-[10px] font-semibold text-gray-600 uppercase print:px-1.5 print:py-1">SL</th>
                    <th class="px-2 py-1.5 text-left text-[10px] font-semibold text-gray-600 uppercase print:px-1.5 print:py-1">Fee Head</th>
                    <th class="px-2 py-1.5 text-right text-[10px] font-semibold text-gray-600 uppercase print:px-1.5 print:py-1">Amount Paid</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                  <tr 
                    v-for="(detail, index) in payment.payment_details" 
                    :key="detail.id"
                    class="hover:bg-gray-50"
                  >
                    <td class="px-2 py-1.5 print:px-1.5 print:py-1">
                      <div class="flex items-center justify-center w-5 h-5 rounded-full bg-blue-100 print:w-4 print:h-4">
                        <span class="text-[10px] font-semibold text-blue-700">{{ index + 1 }}</span>
                      </div>
                    </td>
                    <td class="px-2 py-1.5 print:px-1.5 print:py-1">
                      <div class="flex items-center gap-1.5">
                        <div class="w-6 h-6 rounded-lg bg-green-100 flex items-center justify-center print:w-5 print:h-5">
                          <i class="fas fa-file-invoice-dollar text-green-600 text-[10px]"></i>
                        </div>
                        <span class="font-medium text-gray-800 text-xs print:text-[11px]">
                          {{ detail.fee_head?.head_name || 'N/A' }}
                        </span>
                      </div>
                    </td>
                    <td class="px-2 py-1.5 text-right print:px-1.5 print:py-1">
                      <span class="font-semibold text-gray-800 text-xs print:text-[11px]">
                        ৳{{ formatAmount(detail.paid_amount) }}
                      </span>
                    </td>
                  </tr>
                </tbody>
                <tfoot class="bg-gradient-to-r from-green-50 to-emerald-50 border-t-2 border-green-200">
                  <tr>
                    <td colspan="2" class="px-2 py-2 text-left print:px-1.5 print:py-1.5">
                      <span class="text-sm font-bold text-gray-800 print:text-xs">Total Amount Paid</span>
                    </td>
                    <td class="px-2 py-2 text-right print:px-1.5 print:py-1.5">
                      <span class="text-base font-bold text-green-700 print:text-sm">
                        ৳{{ formatAmount(payment.total_paid_amount) }}
                      </span>
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>

          <!-- Amount in Words -->
          <div class="mb-3 print:mb-2">
            <div class="bg-yellow-50 rounded-lg p-2 border border-yellow-200 print:p-1.5">
              <div class="flex items-start gap-1.5">
                <i class="fas fa-info-circle text-yellow-600 mt-0.5 text-xs print:text-[10px]"></i>
                <div>
                  <div class="text-[10px] text-yellow-700 font-medium mb-0.5">Amount in Words</div>
                  <div class="font-semibold text-gray-800 text-xs print:text-[11px]">
                    {{ amountInWords }} Taka Only
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Footer Note -->
          <div class="border-t border-gray-200 pt-2.5 mt-2.5 print:pt-2 print:mt-2">
            <div class="flex items-center justify-between print:text-[10px]">
              <div>
                <div class="text-[10px] text-gray-500 mb-0.5">Generated On</div>
                <div class="text-xs font-semibold text-gray-700 print:text-[10px]">{{ currentDateTime }}</div>
              </div>
              <div class="text-right">
                <div class="text-[10px] text-gray-500 mb-1">Authorized Signature</div>
                <div class="w-24 border-t-2 border-gray-300 pt-0.5 print:w-20">
                  <div class="text-[10px] text-gray-500">Accounts</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Footer Message -->
          <div class="mt-2.5 text-center print:mt-1.5">
            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 rounded-full border border-blue-200 print:px-2 print:py-0.5">
              <i class="fas fa-check-circle text-green-600 text-xs print:text-[10px]"></i>
              <span class="text-[10px] font-medium text-gray-700">
                Computer-generated receipt - No signature required
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import AppLayout from '../../Layouts/AppLayout.vue'

const route = useRoute()
const router = useRouter()

// Data
const payment = ref(null)
const loading = ref(true)

// Computed
const currentDateTime = computed(() => {
  const now = new Date()
  return now.toLocaleString('en-GB', { 
    day: '2-digit', 
    month: 'short', 
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
})

const amountInWords = computed(() => {
  if (!payment.value) return ''
  return convertToWords(payment.value.total_paid_amount)
})

// Methods
const fetchPayment = async () => {
  loading.value = true
  try {
    const paymentId = route.params.id
    const response = await axios.get(`/api/payments/${paymentId}`)
    
    if (response.data.success) {
      payment.value = response.data.data
      
      // Fix image path
      if (payment.value.class_wise_student?.student?.student_image) {
        payment.value.class_wise_student.student.student_image_url = 
          `/assets/images/student_images/${payment.value.class_wise_student.student.student_image}`
      } else {
        payment.value.class_wise_student.student.student_image_url = '/assets/images/default-avatar.png'
      }
    }
  } catch (error) {
    console.error('Error fetching payment:', error)
    alert('Failed to load payment details')
    router.push({ name: 'payments.index' })
  } finally {
    loading.value = false
  }
}

const formatAmount = (amount) => {
  return parseFloat(amount || 0).toFixed(2)
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  const d = new Date(date)
  return d.toLocaleDateString('en-GB', { 
    day: '2-digit', 
    month: 'long', 
    year: 'numeric' 
  })
}

const convertToWords = (amount) => {
  const ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine']
  const tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety']
  const teens = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen']
  
  const convertLessThanThousand = (num) => {
    if (num === 0) return ''
    
    let result = ''
    
    if (num >= 100) {
      result += ones[Math.floor(num / 100)] + ' Hundred '
      num %= 100
    }
    
    if (num >= 20) {
      result += tens[Math.floor(num / 10)] + ' '
      num %= 10
    } else if (num >= 10) {
      result += teens[num - 10] + ' '
      return result
    }
    
    if (num > 0) {
      result += ones[num] + ' '
    }
    
    return result
  }
  
  let num = Math.floor(parseFloat(amount))
  
  if (num === 0) return 'Zero'
  
  let words = ''
  
  if (num >= 10000000) {
    words += convertLessThanThousand(Math.floor(num / 10000000)) + 'Crore '
    num %= 10000000
  }
  
  if (num >= 100000) {
    words += convertLessThanThousand(Math.floor(num / 100000)) + 'Lakh '
    num %= 100000
  }
  
  if (num >= 1000) {
    words += convertLessThanThousand(Math.floor(num / 1000)) + 'Thousand '
    num %= 1000
  }
  
  if (num > 0) {
    words += convertLessThanThousand(num)
  }
  
  return words.trim()
}

const printReceipt = () => {
  window.print()
}

// Lifecycle
onMounted(() => {
  fetchPayment()
})
</script>

<style scoped>
/* Hide elements on print */
@media print {
  /* Hide header, buttons, loading */
  .no-print {
    display: none !important;
  }
  
  /* Remove margins from receipt container */
  .no-print-margin {
    margin: 0 !important;
    max-width: 100% !important;
  }
  
  /* Hide ALL page elements except receipt */
  * {
    visibility: hidden !important;
  }
  
  /* Show only receipt and its children */
  #receipt-content,
  #receipt-content * {
    visibility: visible !important;
  }
  
  /* Position receipt at top of page */
  #receipt-content {
    position: absolute !important;
    left: 0 !important;
    top: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
    margin: 0 !important;
    padding: 10px !important;
    box-shadow: none !important;
  }
  
  /* A4 page settings */
  @page {
    margin: 0.5cm;
    size: A4 portrait;
  }
  
  /* Compact all spacing */
  #receipt-content * {
    line-height: 1.3 !important;
  }
  
  #receipt-content .p-3,
  #receipt-content .p-2 {
    padding: 8px !important;
  }
  
  #receipt-content .mb-3,
  #receipt-content .mb-2 {
    margin-bottom: 8px !important;
  }
  
  #receipt-content .gap-3,
  #receipt-content .gap-2 {
    gap: 6px !important;
  }
  
  /* Compact header */
  #receipt-content .bg-gradient-to-r {
    padding: 10px !important;
  }
  
  /* Compact table */
  #receipt-content table {
    font-size: 10px !important;
  }
  
  #receipt-content table td,
  #receipt-content table th {
    padding: 4px 6px !important;
  }
  
  #receipt-content tfoot td {
    padding: 6px !important;
  }
  
  /* Prevent page breaks */
  #receipt-content .bg-gray-50,
  #receipt-content table,
  #receipt-content .bg-yellow-50 {
    page-break-inside: avoid;
  }
  
  /* Remove border radius */
  #receipt-content,
  #receipt-content * {
    border-radius: 0 !important;
  }
  
  /* Compact images */
  #receipt-content img,
  #receipt-content .w-14,
  #receipt-content .h-14 {
    width: 35px !important;
    height: 35px !important;
  }
  
  /* Make text smaller */
  #receipt-content {
    font-size: 11px !important;
  }
  
  /* Hide borders on print for cleaner look */
  #receipt-content .border {
    border-color: #e5e7eb !important;
  }
  
  /* Hide breadcrumbs if any */
  nav[aria-label="Breadcrumb"],
  .breadcrumb {
    display: none !important;
  }
}

.fa-spinner.fa-spin {
  animation: fa-spin 1s infinite linear;
}

@keyframes fa-spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>