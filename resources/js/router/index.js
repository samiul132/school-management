import { createRouter, createWebHistory } from 'vue-router'
import { authStore } from '../stores/auth'

// Dashboard routes
import Dashboard from '../Pages/dashboard.vue'

// Login Register routes
import Login from '../pages/auth/Login.vue'
import Register from '../pages/auth/Register.vue' 

// SchoolSettings routes
import SchoolSettingsIndex from '../pages/school-settings/Index.vue'
import SchoolSettingsCreate from '../pages/school-settings/Create.vue'
import SchoolSettingsEdit from '../pages/school-settings/Edit.vue'
import MySchoolSettingsEdit from '../pages/school-settings/MySettings.vue'
import SchoolSettingsShow from '../pages/school-settings/Show.vue'

// SessionManagement routes
import SessionManagementIndex from '../pages/session-managements/Index.vue'
// MonthManagement routes
import MonthManagementIndex from '../pages/month-managements/Index.vue'
// ShiftManagement routes
import ShiftManagementIndex from '../pages/shift-managements/Index.vue'
// ClassManagement routes
import ClassManagementIndex from '../pages/class-managements/Index.vue'
// VersionManagement routes
import VersionManagementIndex from '../pages/version-managements/Index.vue'
// SectionManagement routes
import SectionManagementIndex from '../pages/section-managements/Index.vue'

// StudentProfile routes
import StudentProfileIndex from '../pages/student-profiles/Index.vue'
import StudentProfileCreate from '../pages/student-profiles/Create.vue'
import StudentProfileEdit from '../pages/student-profiles/Edit.vue'
import StudentProfileShow from '../pages/student-profiles/Show.vue'
import StudentIdCardShow from '../pages/student-profiles/IdCardShow.vue'

// CashBanks route
import CashBankIndex from '../pages/cash-banks/Index.vue'
import CashBankCreate from '../pages/cash-banks/Create.vue'
import CashBankEdit from '../pages/cash-banks/Edit.vue'
import CashBankShow from '../pages/cash-banks/Show.vue'

// CashTransfer routes
import CashTransferIndex from '../pages/cashtransfer/Index.vue'
import CashTransferCreate from '../pages/cashtransfer/Create.vue'
import CashTransferEdit from '../pages/cashtransfer/Edit.vue'
import CashTransferShow from '../pages/cashtransfer/Show.vue'

// AccountHeads routes
import AccountHeadsIndex from '../pages/accountheads/Index.vue'
import AccountHeadsCreate from '../pages/accountheads/Create.vue'
import AccountHeadsEdit from '../pages/accountheads/Edit.vue'

// Subsidiaries routes
import SubsidiariesIndex from '../pages/subsidiaries/Index.vue'
import SubsidiariesCreate from '../pages/subsidiaries/Create.vue'
import SubsidiariesEdit from '../pages/subsidiaries/Edit.vue'

// FeeHead routes
import FeeHeadIndex from '../pages/fee-heads/Index.vue'

// Waver routes
import WaverIndex from '../pages/wavers/Index.vue'
import WaverCreate from '../pages/wavers/Create.vue'
import WaverEdit from '../pages/wavers/Edit.vue'

// Payment routes
import PaymentIndex from '../pages/payments/Index.vue'
import PaymentCreate from '../pages/payments/Create.vue'
import PaymentEdit from '../pages/payments/Edit.vue'
import PaymentShow from '../pages/payments/Show.vue'

// FeeAssign routes
import FeeAssignIndex from '../pages/fee-assigns/Index.vue'
import FeeAssignCreate from '../pages/fee-assigns/Create.vue'
import FeeAssignEdit from '../pages/fee-assigns/Edit.vue'
import FeeAssignShow from '../pages/fee-assigns/Show.vue'


// All Voucher routes
import VouchersIndex from '../pages/vouchers/Index.vue'
import VouchersCreate from '../pages/vouchers/Create.vue'
import VouchersEdit from '../pages/vouchers/Edit.vue'
import VouchersShow from '../pages/vouchers/Show.vue'

// CashInVoucher routes
import CashInVoucherIndex from '../pages/cash-in-vouchers/Index.vue'
import CashInVoucherCreate from '../pages/cash-in-vouchers/Create.vue'
import CashInVoucherEdit from '../pages/cash-in-vouchers/Edit.vue'
import CashInVoucherShow from '../pages/cash-in-vouchers/Show.vue'

// CashOutVoucher routes
import CashOutVoucherIndex from '../pages/cash-out-vouchers/Index.vue'
import CashOutVoucherCreate from '../pages/cash-out-vouchers/Create.vue'
import CashOutVoucherEdit from '../pages/cash-out-vouchers/Edit.vue'
import CashOutVoucherShow from '../pages/cash-out-vouchers/Show.vue'

// Subject Management routes
import SubjectManagementIndex from '../pages/subjects/Index.vue'

// Subject Assign routes
import SubjectAssignIndex from '../pages/subject-assign/Index.vue'
import SubjectAssignCreate from '../pages/subject-assign/Create.vue'
import SubjectAssignEdit from '../pages/subject-assign/Edit.vue'

// Designations routes
import DesignationsIndex from '../pages/designations/Index.vue'
import DesignationsCreate from '../pages/designations/Create.vue'
import DesignationsEdit from '../pages/designations/Edit.vue' 

// staffs routes
import StaffsIndex from '../pages/staffs/Index.vue'
import StaffsCreate from '../pages/staffs/Create.vue'
import StaffsEdit from '../pages/staffs/Edit.vue'

// attendance routes
import AttendanceIndex from '../pages/attendance/Index.vue'
import AttendanceCreate from '../pages/attendance/Create.vue'

// Leave routes
import LeaveIndex from '../pages/leave/Index.vue'
import LeaveCreate from '../pages/leave/Create.vue'
import LeaveEdit from '../pages/leave/Edit.vue'

// Advance Payment routes
import AdvancePaymentIndex from '../pages/advance-payment/Index.vue'
import AdvancePaymentCreate from '../pages/advance-payment/Create.vue'
import AdvancePaymentEdit from '../pages/advance-payment/Edit.vue'

// Over Time routes
import OverTimeIndex from '../pages/over-time/Index.vue'
import OverTimeCreate from '../pages/over-time/Create.vue'
import OverTimeEdit from '../pages/over-time/Edit.vue'

// Staff-salary routes
import StaffSalaryIndex from '../pages/staff-salary/Index.vue'
import StaffSalaryCreate from '../pages/staff-salary/Create.vue'
import StaffSalaryEdit from '../pages/staff-salary/Edit.vue'

// Staff-salary-payment routes
import StaffSalaryPaymentIndex from '../pages/staff-salary-payment/Index.vue'
import StaffSalaryPaymentCreate from '../pages/staff-salary-payment/Create.vue'
import StaffSalaryPaymentEdit from '../pages/staff-salary-payment/Edit.vue'

// Post Notification routes
import PostNotificationIndex from '../pages/post-notifications/Index.vue'
// App Slider routes
import AppSliderIndex from '../pages/app-slider/Index.vue'

// Class Routine routes
import ClassRoutineIndex from '../pages/class-routines/Index.vue'
import ClassRoutineCreate from '../pages/class-routines/Create.vue'
import ClassRoutineEdit from '../pages/class-routines/Edit.vue'
import ClassRoutineShow from '../pages/class-routines/Show.vue'

// Student Attendance routes
import StudentAttendanceIndex from '../pages/student-attendance/Index.vue'
import StudentAttendanceCreate from '../pages/student-attendance/Create.vue'

const routes = [
  { 
    path: '/login', 
    name: 'login',  
    component: Login,
    meta: { guest: true }
  },
  { 
    path: '/register', 
    name: 'register',  
    component: Register,
    meta: { guest: true }
  },
  
  // Dashboard routes
  {
    path: '/',
    name: 'dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  },

  // SchoolSettings routes
  {
    path: '/school-settings',
    name: 'school-settings.index',
    component: SchoolSettingsIndex,
    meta: { requiresAuth: true }
  },
  {
    path: '/school-settings/create',
    name: 'school-settings.create',
    component: SchoolSettingsCreate,
    meta: { requiresAuth: true }
  },
  {
    path: '/school-settings/:id/edit',
    name: 'school-settings.edit',
    component: SchoolSettingsEdit,
    props: true,
    meta: { requiresAuth: true }
  },
  {
    path: '/my-school-settings',
    name: 'school-settings.MySettings',
    component: MySchoolSettingsEdit,
    props: true,
    meta: { requiresAuth: true }
  },
  {
    path: '/school-settings/:id',
    name: 'school-settings.show',
    component: SchoolSettingsShow,
    props: true,
    meta: { requiresAuth: true }
  },

  // SessionManagement routes
  {
    path: '/session-managements',
    name: 'session-managements.index',
    component: SessionManagementIndex,
    meta: { requiresAuth: true }
  },

  // MonthManagement routes
  {
    path: '/month-managements',
    name: 'month-managements.index',
    component: MonthManagementIndex,
    meta: { requiresAuth: true }
  },

  // ShiftManagement routes
  {
    path: '/shift-managements',
    name: 'shift-managements.index',
    component: ShiftManagementIndex,
    meta: { requiresAuth: true }
  },

  // ClassManagement routes
  {
    path: '/class-managements',
    name: 'class-managements.index',
    component: ClassManagementIndex,
    meta: { requiresAuth: true }
  },

  // VersionManagement routes
  {
    path: '/version-managements',
    name: 'version-managements.index',
    component: VersionManagementIndex,
    meta: { requiresAuth: true }
  },

  // SectionManagement routes
  {
    path: '/section-managements',
    name: 'section-managements.index',
    component: SectionManagementIndex,
    meta: { requiresAuth: true }
  },

  // StudentProfile routes
  {
    path: '/student-profiles',
    name: 'student-profiles.index',
    component: StudentProfileIndex,
    meta: { requiresAuth: true }
  },
  {
    path: '/student-profiles/create',
    name: 'student-profiles.create',
    component: StudentProfileCreate,
    meta: { requiresAuth: true }
  },
  {
    path: '/student-profiles/:id/edit',
    name: 'student-profiles.edit',
    component: StudentProfileEdit,
    props: true,
    meta: { requiresAuth: true }
  },
  {
    path: '/student-profiles/:id',
    name: 'student-profiles.show',
    component: StudentProfileShow,
    props: true,
    meta: { requiresAuth: true }
  },
  {
    path: '/student-profiles/:id/id-card',
    name: 'student-profiles.idcardshow',
    component: StudentIdCardShow,
    props: true,
    meta: { requiresAuth: true }
  },

  // CashBank routes
  {
    path: '/cash-banks',
    name: 'cash-banks.index',
    component: CashBankIndex,
    meta: { requiresAuth: true }
  },
  {
    path: '/cash-banks/create',
    name: 'cash-banks.create',
    component: CashBankCreate,
    meta: { requiresAuth: true }
  },
  {
    path: '/cash-banks/:id/edit',
    name: 'cash-banks.edit',
    component: CashBankEdit,
    props: true,
    meta: { requiresAuth: true }
  },
  {
    path: '/cash-banks/:id',
    name: 'cash-banks.show',
    component: CashBankShow,
    props: true,
    meta: { requiresAuth: true }
  },

  // CashTransfer routes
  {
    path: '/cash-transfers',
    name: 'cash-transfers.index',
    component: CashTransferIndex,
    meta: { requiresAuth: true }
  },
  {
    path: '/cash-transfers/create',
    name: 'cash-transfers.create',
    component: CashTransferCreate,
    meta: { requiresAuth: true }
  },
  {
    path: '/cash-transfers/:id/edit',
    name: 'cash-transfers.edit',
    component: CashTransferEdit,
    props: true,
    meta: { requiresAuth: true }
  },
  {
    path: '/cash-transfers/:id',
    name: 'cash-transfers.show',
    component: CashTransferShow,
    props: true,
    meta: { requiresAuth: true }
  },

  // AccountHeads routes
  {
    path: '/accountheads',
    name: 'accountheads.index',
    component: AccountHeadsIndex,
    meta: { requiresAuth: true }
  },
  {
    path: '/accountheads/create',
    name: 'accountheads.create',
    component: AccountHeadsCreate,
    meta: { requiresAuth: true }
  },
  {
    path: '/accountheads/:id/edit',
    name: 'accountheads.edit',
    component: AccountHeadsEdit,
    props: true,
    meta: { requiresAuth: true }
  },

  // Subsidiaries routes
  {
    path: '/subsidiaries',
    name: 'subsidiaries.index',
    component: SubsidiariesIndex,
    meta: { requiresAuth: true }
  },
  {
    path: '/subsidiaries/create',
    name: 'subsidiaries.create',
    component: SubsidiariesCreate,
    meta: { requiresAuth: true }
  },
  {
    path: '/subsidiaries/:id/edit',
    name: 'subsidiaries.edit',
    component: SubsidiariesEdit,
    props: true,
    meta: { requiresAuth: true }
  },

  // FeeHead routes
  {
    path: '/fee-heads',
    name: 'fee-heads.index',
    component: FeeHeadIndex,
    meta: { requiresAuth: true }
  },

  // Waver routes
  {
    path: '/wavers',
    name: 'wavers.index',
    component: WaverIndex,
    meta: { requiresAuth: true }
  },
  {
    path: '/wavers/create',
    name: 'wavers.create',
    component: WaverCreate,
    meta: { requiresAuth: true }
  },
  {
    path: '/wavers/student/:class_wise_student_id/edit',
    name: 'wavers.student.edit',
    component: WaverEdit,
    props: true,
    meta: { requiresAuth: true }
  },

  // Payment routes
  {
    path: '/payments',
    name: 'payments.index',
    component: PaymentIndex,
    meta: { requiresAuth: true }
  },
  {
    path: '/payments/create',
    name: 'payments.create',
    component: PaymentCreate,
    meta: { requiresAuth: true }
  },
  {
    path: '/payments/:id/edit',
    name: 'payments.edit',
    component: PaymentEdit,
    props: true,
    meta: { requiresAuth: true }
  },
  {
    path: '/payments/:id',
    name: 'payments.show',
    component: PaymentShow,
    props: true,
    meta: { requiresAuth: true }
  },

  // FeeAssign routes
  {
    path: '/fee-assigns',
    name: 'fee-assigns.index',
    component: FeeAssignIndex,
    meta: { requiresAuth: true }
  },
  {
    path: '/fee-assigns/create',
    name: 'fee-assigns.create',
    component: FeeAssignCreate,
    meta: { requiresAuth: true }
  },
  {
    path: '/fee-assigns/:id/edit',
    name: 'fee-assigns.edit',
    component: FeeAssignEdit,
    props: true,
    meta: { requiresAuth: true }
  },
  {
    path: '/fee-assigns/:id',
    name: 'fee-assigns.show',
    component: FeeAssignShow,
    props: true,
    meta: { requiresAuth: true }
  },

  // All Vouchers routes
  {
    path: '/vouchers',
    name: 'vouchers.index',
    component: VouchersIndex,
    meta: { requiresAuth: true }
  },
  {
    path: '/vouchers/create',
    name: 'vouchers.create',
    component: VouchersCreate,
    meta: { requiresAuth: true }
  },
  {
    path: '/vouchers/:id/edit',
    name: 'vouchers.edit',
    component: VouchersEdit,
    props: true,
    meta: { requiresAuth: true }
  },
  {
    path: '/vouchers/:id',
    name: 'vouchers.show',
    component: VouchersShow,
    props: true,
    meta: { requiresAuth: true }
  },

  // CashInVoucher routes
  {
    path: '/cash-in-vouchers',
    name: 'cash-in-vouchers.index',
    component: CashInVoucherIndex,
    meta: { requiresAuth: true }
  },
  {
    path: '/cash-in-vouchers/create',
    name: 'cash-in-vouchers.create',
    component: CashInVoucherCreate,
    meta: { requiresAuth: true }
  },
  {
    path: '/cash-in-vouchers/:id/edit',
    name: 'cash-in-vouchers.edit',
    component: CashInVoucherEdit,
    props: true,
    meta: { requiresAuth: true }
  },
  {
    path: '/cash-in-vouchers/:id',
    name: 'cash-in-vouchers.show',
    component: CashInVoucherShow,
    props: true,
    meta: { requiresAuth: true }
  },

  // CashOutVoucher routes
  {
    path: '/cash-out-vouchers',
    name: 'cash-out-vouchers.index',
    component: CashOutVoucherIndex,
    meta: { requiresAuth: true }
  },
  {
    path: '/cash-out-vouchers/create',
    name: 'cash-out-vouchers.create',
    component: CashOutVoucherCreate,
    meta: { requiresAuth: true }
  },
  {
    path: '/cash-out-vouchers/:id/edit',
    name: 'cash-out-vouchers.edit',
    component: CashOutVoucherEdit,
    props: true,
    meta: { requiresAuth: true }
  },
  {
    path: '/cash-out-vouchers/:id',
    name: 'cash-out-vouchers.show',
    component: CashOutVoucherShow,
    props: true,
    meta: { requiresAuth: true }
  },

  // Subject Management routes
  {
    path: '/subjects',
    name: 'subjects.index',
    component: SubjectManagementIndex,
    meta: { requiresAuth: true }
  },

  // SubjectAssign routes
  {
    path: '/subject-assign',
    name: 'subject-assign.index',
    component: SubjectAssignIndex,
    meta: { requiresAuth: true }
  },
  {
    path: '/subject-assign/create',
    name: 'subject-assign.create',
    component: SubjectAssignCreate,
    meta: { requiresAuth: true }
  },
  {
    path: '/subject-assign/:id/edit',
    name: 'subject-assign.edit',
    component: SubjectAssignEdit,
    props: true,
    meta: { requiresAuth: true }
  },

  // Designation routes
  {
    path: '/designations',
    name: 'designations.index',
    component: DesignationsIndex,
    meta: { requiresAuth: true }
  },
  {
    path: '/designations/create',
    name: 'designations.create',
    component: DesignationsCreate,
    meta: { requiresAuth: true }
  },
  {
    path: '/designations/:id/edit',
    name: 'designations.edit',
    component: DesignationsEdit,
    props: true,
    meta: { requiresAuth: true }
  },

  // Satffs routes
  {
    path: '/staffs',
    name: 'staffs.index',
    component: StaffsIndex,
    meta: { requiresAuth: true }
  },
  {
    path: '/staffs/create',
    name: 'staffs.create',
    component: StaffsCreate,
    meta: { requiresAuth: true }
  },
  {
    path: '/staffs/:id/edit',
    name: 'staffs.edit',
    component: StaffsEdit,
    props: true,
    meta: { requiresAuth: true }
  },

  // Attendence
  {
    path: '/attendance',
    name: 'attendance.index',
    component: AttendanceIndex,
    meta: { requiresAuth: true }
  },
  {
    path: '/attendance/create',
    name: 'attendance.create',
    component: AttendanceCreate,
  },

  // leave routes
  {
    path: '/leave',
    name: 'leave.index',
    component: LeaveIndex,
    meta: { requiresAuth: true }
  },
  {
    path: '/leave/create',
    name: 'leave.create',
    component: LeaveCreate,
    meta: { requiresAuth: true }
  },
  {
    path: '/leave/:id/edit',
    name: 'leave.edit',
    component: LeaveEdit,
    props: true,
    meta: { requiresAuth: true }
  },

  // Advance-payment routes
  {
    path: '/advance-payment',
    name: 'advance-payment.index',
    component: AdvancePaymentIndex,
    meta: { requiresAuth: true }
  },
  {
    path: '/advance-payment/create',
    name: 'advance-payment.create',
    component: AdvancePaymentCreate,
    meta: { requiresAuth: true }
  },
  {
    path: '/advance-payment/:id/edit',
    name: 'advance-payment.edit',
    component: AdvancePaymentEdit,
    props: true,
    meta: { requiresAuth: true }
  },

  // Over-time routes
  {
    path: '/over-time',
    name: 'over-time.index',
    component: OverTimeIndex,
    meta: { requiresAuth: true }
  },
  {
    path: '/over-time/create',
    name: 'over-time.create',
    component: OverTimeCreate,
    meta: { requiresAuth: true }
  },
  {
    path: '/over-time/:id/edit',
    name: 'over-time.edit',
    component: OverTimeEdit,
    props: true,
    meta: { requiresAuth: true }
  },

  // Staff-salary routes
  {
    path: '/staff-salary',
    name: 'staff-salary.index',
    component: StaffSalaryIndex,
    meta: { requiresAuth: true }
  },
  {
    path: '/staff-salary/create',
    name: 'staff-salary.create',
    component: StaffSalaryCreate,
    meta: { requiresAuth: true }
  },
  {
    path: '/staff-salary/:id/edit',
    name: 'staff-salary.edit',
    component: StaffSalaryEdit,
    props: true,
    meta: { requiresAuth: true }
  },

  // Staff-salary-payment routes
  {
    path: '/staff-salary-payment',
    name: 'staff-salary-payment.index',
    component: StaffSalaryPaymentIndex,
    meta: { requiresAuth: true }
  },
  {
    path: '/staff-salary-payment/create',
    name: 'staff-salary-payment.create',
    component: StaffSalaryPaymentCreate,
    meta: { requiresAuth: true }
  },
  {
    path: '/staff-salary-payment/:id/edit',
    name: 'staff-salary-payment.edit',
    component: StaffSalaryPaymentEdit,
    props: true,
    meta: { requiresAuth: true }
  },

  // Post Notification routes
  {
    path: '/post-notifications',
    name: 'post-notifications.index',
    component: PostNotificationIndex,
    meta: { requiresAuth: true }
  },

  // App Slider routes
  {
    path: '/app-slider',
    name: 'app-slider.index',
    component: AppSliderIndex,
    meta: { requiresAuth: true }
  },

  // Class Routine routes
  {
    path: '/class-routines',
    name: 'class-routines.index',
    component: ClassRoutineIndex,
    meta: { requiresAuth: true }
  },
  {
    path: '/class-routines/create',
    name: 'class-routines.create',
    component: ClassRoutineCreate,
    meta: { requiresAuth: true }
  },
  {
    path: '/class-routines/:id/edit',
    name: 'class-routines.edit',
    component: ClassRoutineEdit,
    props: true,
    meta: { requiresAuth: true }
  },
  {
    path: '/class-routines/:id',
    name: 'class-routines.show',
    component: ClassRoutineShow,
    props: true,
    meta: { requiresAuth: true }
  },

  // Student Attendance routes
  {
    path: '/student-attendance',
    name: 'student-attendance.index',
    component: StudentAttendanceIndex,
    meta: { requiresAuth: true }
  },
  {
    path: '/student-attendance/create',
    name: 'student-attendance.create',
    component: StudentAttendanceCreate,
    meta: { requiresAuth: true }
  },
  

]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const isAuthenticated = authStore.state.isAuthenticated

  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/login')
  } else if (to.meta.guest && isAuthenticated) {
    next('/')
  } else {
    next()
  }
})

export default router