<template>
    <v-container>
      <v-row>
        <v-col cols="6"> 
          <v-form>
            <v-row>
              <v-col>
                <v-row>
                    <v-col>
                        <v-label for="client">Select Client:</v-label>
                    </v-col>
                    <v-col>
                        <v-select v-model="selectedClient" :items="clients" label="Client"></v-select>
                    </v-col>
                </v-row>
            </v-col>
              <v-col  cols="6">
                <v-file-input
                  v-model="selectedFile"
                  label="Choose Excel File"
                  accept=".xls, .xlsx"
                ></v-file-input>
              </v-col>
     
              <v-col>
                <v-btn @click="uploadFile" color="primary">Upload Files</v-btn>
              </v-col>
            </v-row>
          </v-form>
        </v-col>
      </v-row>
    </v-container>
  </template>
  





  <script>
  export default {
    data() {
      return {
        selectedClient: null,
        clients: [
          { id: 1, name: 'Client 1' },
          { id: 2, name: 'Client 2' },
        ],
        selectedFile: null,
      };
    },
    methods: {
      uploadFile() {
        if (this.selectedFile && this.selectedClient !== null) {
          const formData = new FormData();
          formData.append('file', this.selectedFile);
          formData.append('client_id', this.selectedClient);
  
        } else {
          this.$vuetify.snackbar.show({
            text: 'Please select a file and choose a client.',
            color: 'error',
          });
        }
      },
    },
  };
  </script>
  