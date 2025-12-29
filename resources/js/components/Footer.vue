<template>
  <footer class="py-6 border-t border-gray-200 dark:border-gray-700 px-4">
    <div class="max-w-7xl mx-auto">
      <div
        class="flex flex-col md:flex-row justify-between items-center gap-4 text-center md:text-left"
      >
        <div class="text-gray-600 dark:text-gray-400 text-xs md:text-sm">
          Designed & Developed by
          <a
            href="https://www.designcodeit.com/"
            target="_blank"
            class="font-semibold 
                  transition-all 
                  relative 
                  hover:text-transparent 
                  bg-linear-to-r from-blue-600 to-orange-500 
                  bg-clip-text
                  after:content-[''] 
                  after:absolute 
                  after:left-0 
                  after:bottom-0 
                  after:w-full 
                  after:h-0.5 
                  after:bg-linear-to-r
                  after:from-blue-600 
                  after:to-orange-500 
                  after:scale-x-0 
                  after:origin-left 
                  after:transition-transform 
                  after:duration-300
                  hover:after:scale-x-100"
          >
            Designcode IT
          </a>
        </div>

        <div class="text-gray-600 dark:text-gray-400 text-xs md:text-sm">
          © {{ new Date().getFullYear() }}
          <span class="font-semibold text-gray-800 dark:text-gray-100">{{ schoolName }} & Designcode IT</span>. 
          All rights reserved.
        </div>
      </div>
    </div>
  </footer>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useSchoolStore } from '@/stores/schoolStore';

export default {
  name: 'Footer',
  setup() {
    const schoolStore = useSchoolStore();
    
    onMounted(async () => {
      if (!schoolStore.settings) {
        try {
          await schoolStore.fetchSchoolSettings();
        } catch (error) {
          console.error('Failed to fetch school settings:', error);
        }
      }
    });
    
    const schoolName = computed(() => schoolStore.getSchoolName());
    
    return {
      schoolName
    };
  }
};
</script>