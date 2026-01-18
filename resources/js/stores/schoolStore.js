import { defineStore } from 'pinia';
import { ref } from 'vue';
import axios from 'axios';

export const useSchoolStore = defineStore('school', () => {
  const settings = ref(null);
  const loading = ref(false);
  const error = ref(null);

  async function fetchSchoolSettings() {
    try {
      loading.value = true;
      error.value = null;
      
      console.log('Fetching school settings from API...');
      const response = await axios.get('/api/current-school-settings');
      
      console.log('API Response:', response.data);
      
      if (response.data.success) {
        settings.value = response.data.data;
        localStorage.setItem('school_settings', JSON.stringify(response.data.data));
        console.log('Settings saved:', settings.value);
      }
      
      return settings.value;
    } catch (err) {
      error.value = err;
      console.error('Failed to fetch school settings:', err);
      
      const savedSettings = localStorage.getItem('school_settings');
      if (savedSettings) {
        console.log('Loading from localStorage as fallback');
        try {
          settings.value = JSON.parse(savedSettings);
        } catch (parseError) {
          console.error('Error parsing localStorage:', parseError);
          localStorage.removeItem('school_settings');
        }
      }
      
      return settings.value;
    } finally {
      loading.value = false;
    }
  }

  function getSchoolName() {
    return settings.value?.school_name || 'School Management';
  }

  function getSchoolLogo() {
    if (settings.value?.logo) {
      return settings.value.logo_url || `/assets/images/school_logo/${settings.value.logo}`;
    }
    return '/images/default-logo.png';
  }

  function getSchoolEmail() {
    return settings.value?.email || '';
  }

  function getSchoolAddress() {
    return settings.value?.address || '';
  }
  function getSchoolSmsBanalce() {
    return settings.value?.sms_balance || '';
  }

  function getSchoolMobile() {
    return settings.value?.mobile_number || '';
  }

  function clearSettings() {
    settings.value = null;
    localStorage.removeItem('school_settings');
  }

  const savedSettings = localStorage.getItem('school_settings');
  if (savedSettings) {
    try {
      settings.value = JSON.parse(savedSettings);
      console.log('Initialized from localStorage:', settings.value);
    } catch (e) {
      console.error('Error parsing saved settings:', e);
      localStorage.removeItem('school_settings');
    }
  }

  return {
    settings,
    loading,
    error,
    fetchSchoolSettings,
    getSchoolName,
    getSchoolLogo,
    getSchoolEmail,
    getSchoolAddress,
    getSchoolSmsBanalce,
    getSchoolMobile,
    clearSettings
  };
});