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
          class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-600 hover:bg-green-700 rounded-lg text-white text-sm font-medium transition-colors cursor-pointer"
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
    <div v-if="!loading && payment" class="mx-auto no-print-margin">
      <!-- Receipt Card -->
      <div id="receipt-content" class="bg-white shadow-lg overflow-hidden">
        <printHeader/>

        <!-- Receipt Body -->
        <div>
          <!-- Student Information -->
          <div class="print:mb-1">
            <div class="p-1 print:p-1">
              
              <div class="flex justify-between items-center">
                <div class="flex-1 text-start">
                  <span class="text-[12px] font-semibold text-black print:text-[10px]">
                    Payment ID: #{{ payment.id }}
                  </span>
                </div>

                <div class="bg-white flex justify-center items-center border-b-4 border-double border-black" style="width: fit-content; margin: 0 auto;">
                  <div class="px-2">
                    <p class="text-xs font-bold text-black text-center">
                      Payment Receipt
                    </p>
                  </div>
                </div>

                <div class="flex-1 text-right">
                  <span class="text-[12px] font-semibold text-black print:text-[10px]">
                    Date: {{ formatDate(payment.payment_date) }}
                  </span>
                </div>
              </div>

              <!-- Payment Receipt -->
              

              <!-- Student Details - Name and ID -->
              <div class="flex justify-between items-center gap-3 pb-2 border-b border-dashed border-gray-400 mt-2 print:border-gray-300">
                <div class="flex-1">
                  <span class="text-[12px] text-black font-semibold">
                    Student Name: {{ payment.class_wise_student?.student?.student_name || 'N/A' }}
                  </span>
                </div>
                <div class="text-right">
                  <span class="text-[12px] text-black font-semibold">
                    Student ID: {{ payment.class_wise_student?.student?.id_card_number || 'N/A' }}
                  </span>
                </div>
              </div>

              <!-- Academic Info - Version, Class, Section and Roll -->
              <div class="grid grid-cols-4 gap-4 pb-2 border-b border-dashed border-gray-400 mt-2 print:border-gray-300">
                <div>
                  <span class="text-[12px] text-black font-medium">
                    {{ payment.class_wise_student?.version?.version_name || 'N/A' }}
                  </span>
                </div>
                <div>
                  <span class="text-[12px] text-black font-medium">
                    {{ payment.class_wise_student?.class?.class_name || 'N/A' }}
                  </span>
                </div>
                <div>
                  <span class="text-[12px] text-black font-medium">
                    {{ payment.class_wise_student?.section?.section_name || 'N/A' }}
                  </span>
                </div>
                <div class="text-right">
                  <span class="text-[12px] text-black font-medium">
                    Class Roll: {{ payment.class_wise_student?.class_roll || 'N/A' }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Payment Details -->
          <div class="mb-3 print:mb-2">

            <!-- Fee Details Table -->
            <div class="overflow-hidden">
              <table class="w-full">
                <thead class="bg-gray-500">
                  <tr>
                    <th class="px-2 py-2 text-left text-[10px] font-semibold text-white uppercase print:px-2 print:py-1 border-b border-gray-600 print:border-b-0" style="width: 40px;">SL</th>
                    <th class="px-2 py-2 text-left text-[10px] font-semibold text-white uppercase print:px-2 print:py-1 border-b border-gray-600 print:border-b-0">Fee Head</th>
                    <th class="px-2 py-2 text-right text-[10px] font-semibold text-white uppercase print:px-2 print:py-1 border-b border-gray-600 print:border-b-0">Amount Paid</th>
                  </tr>
                </thead>
                <tbody>
                  <tr 
                      v-for="(detail, index) in groupedPaymentDetails" 
                      :key="detail.head_id"
                      :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-100'"
                      class="border-b border-gray-300"
                    >
                    <td class="px-2 py-1.5 print:px-1.5 print:py-1" style="width: 40px;">
                      <div class="flex items-center justify-left">
                        <span class="text-[10px] font-semibold text-black">{{ index + 1 }}</span>
                      </div>
                    </td>
                    <td class="px-2 py-1.5 print:px-1.5 print:py-1">
                      <span class="font-medium text-black text-xs print:text-[11px]">
                        {{ detail.fee_head?.head_name || 'N/A' }}
                      </span>
                    </td>
                    <td class="px-2 py-1.5 text-right print:px-1.5 print:py-1">
                      <span class="font-semibold text-black text-xs print:text-[11px]">
                        {{ formatAmount(detail.paid_amount) }}/=	
                      </span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            
            <!-- Total Payment Amount -->
            <div class="mt-0 px-2 py-2 print:px-1.5 print:py-1 flex justify-between items-center">
              <span class="text-xs text-black print:text-xs">Amount in Words: {{ amountInWords }} Taka Only</span>
              <span class="text-base font-bold text-black print:text-sm">
                Total Payment Amount : {{ formatAmount(payment.total_paid_amount) }}/=	
              </span>
            </div>
          </div>

          <!-- Footer Note -->
          <div class="pt-8 mt-8 print:pt-16 print:mt-16">
            <div class="flex items-center justify-end print:text-[10px]">
              <div class="text-center">
                <div class="pt-0.5 print:w-auto" style="border-top: 1px solid black; min-width: 120px;">
                  <div class="text-[10px] text-black mb-1">Authorized Signature</div>
                </div>
              </div>
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
import printHeader from '../../components/printHeader.vue'

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

const groupedPaymentDetails = computed(() => {
  if (!payment.value?.payment_details) return []
  
  const grouped = {}
  
  payment.value.payment_details.forEach(detail => {
    const headId = detail.fee_head?.id || detail.head_id
    const headName = detail.fee_head?.head_name || 'N/A'
    
    if (!grouped[headId]) {
      grouped[headId] = {
        head_id: headId,
        fee_head: { head_name: headName },
        paid_amount: 0
      }
    }
    
    grouped[headId].paid_amount += parseFloat(detail.paid_amount || 0)
  })
  
  return Object.values(grouped)
})

const formatAmount = (amount) => {
  return parseFloat(amount || 0).toFixed(0)
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  const d = new Date(date)
  return d.toLocaleDateString('en-GB', { 
    day: '2-digit', 
    month: 'short', 
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

  #receipt-content .pt-8 {
    padding-top: 20px !important;
  }

  #receipt-content .mt-8 {
    margin-top: 20px !important;
  }
  
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
    size: A4 portrait !important;
  }
  
  /* Increase spacing for print */
  #receipt-content * {
    line-height: 1.4 !important;
  }
  
  #receipt-content .p-3,
  #receipt-content .p-2,
  #receipt-content .p-1 {
    padding: 8px !important;
  }
  
  #receipt-content .mb-3,
  #receipt-content .mb-2,
  #receipt-content .mb-1 {
    margin-bottom: 10px !important;
  }
  
  #receipt-content .mt-2 {
    margin-top: 8px !important;
  }
  
  #receipt-content .gap-3,
  #receipt-content .gap-2 {
    gap: 8px !important;
  }
  
  #receipt-content .pb-2 {
    padding-bottom: 8px !important;
  }
  
  #receipt-content .pt-2,
  #receipt-content .pt-2\.5 {
    padding-top: 10px !important;
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
    padding: 6px !important;
  }
  
  #receipt-content .bg-gray-200 {
    padding: 6px 8px !important;
  }
  
  /* Prevent page breaks */
  #receipt-content .border,
  #receipt-content table {
    page-break-inside: avoid;
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
  
  /* Ensure black borders on print */
  #receipt-content .border {
    border-color: #000000 !important;
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