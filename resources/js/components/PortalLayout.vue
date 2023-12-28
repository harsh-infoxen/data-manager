<template>
    <v-app>
      <v-app-bar app>
        <v-btn @click="openFileInput" color="primary">Upload Files</v-btn>
        <v-btn @click="removeDuplicates" color="success">Remove Duplicates and Download</v-btn>
        <v-btn @click="importFiles" color="info">Import</v-btn>
        
        <input
          ref="fileInput"
          type="file"
          style="display: none"
          @change="handleFileChange"
          multiple
        />
      </v-app-bar>
    </v-app>
  </template>
  
  <script>
  export default {
    methods: {
      openFileInput() {
        this.$refs.fileInput.click();
      },
      handleFileChange(event) {
        const selectedFiles = event.target.files;
        console.log('Selected Files:', selectedFiles);
      },
      removeDuplicates() {
        console.log('Removing duplicates...');
      },
      importFiles() {
        console.log('Importing files...');
      },
    },
  };
  </script>
  
  <style scoped>
 
  </style>
  