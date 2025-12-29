import { useSchoolStore } from '@/stores/schoolStore';

export default {
  install(app) {
    const schoolStore = useSchoolStore();
    
    app.config.globalProperties.$school = {
      name: () => schoolStore.getSchoolName(),
      logo: () => schoolStore.getSchoolLogo(),
      settings: () => schoolStore.settings.value,
      fetch: () => schoolStore.fetchSchoolSettings()
    };
    
    app.provide('school', {
      name: () => schoolStore.getSchoolName(),
      logo: () => schoolStore.getSchoolLogo(),
      settings: () => schoolStore.settings.value,
      fetch: () => schoolStore.fetchSchoolSettings()
    });
    
    app.component('SchoolName', {
      template: '<span>{{ $school.name() }}</span>'
    });
    
    app.component('SchoolLogo', {
      template: '<img :src="$school.logo()" class="school-logo" />'
    });
  }
};