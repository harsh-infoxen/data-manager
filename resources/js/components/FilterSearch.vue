<template>
  <v-container>
    <v-row class="header">
      <!-- Upload buttons -->
      <v-col cols="6">
        <v-file-input
          clearable
          @click:clear="clearFile"
          v-model="selectedFile"
          label="Choose Excel File"
          accept=".xls, .xlsx"
          @change="handleFileChange"
        ></v-file-input>
      </v-col>

      <v-col class="mt-2">
        <v-btn @click="removeDuplicateAndDownload" color="info" class="mr-4"
          >Remove Duplicates and Download</v-btn
        >
        <v-btn @click="saveData" color="info" :disabled="!duplicatesRemoved"
          >Save Data</v-btn
        >
      </v-col>
    </v-row>

    <!--fiters -->
    <v-row class="my-4">
      <v-col>
        <v-text-field v-model="email" label="Search by Email"></v-text-field>
      </v-col>
      <v-col class="row-pointer">
        <v-text-field
          v-model="formattedDate"
          label="Search by Date"
          @click="openDatePickerModal"
          append-icon="mdi-calendar"
          id="slected-dat"
        >
          <v-dialog v-model="datePickerModal" width="290px">
            <div>
              <v-date-picker v-model="date" scrollable>
                <div class="flex-grow-1"></div>
              </v-date-picker>
              <v-row
                class="v-sheet v-theme--customeTheme v-picker v-date-picker v-date-picker--month cutout-mode"
              >
                <v-col>
                  <v-btn text color="primary" @click="closeDatePickerModal"
                    >Cancel</v-btn
                  >
                  <v-btn text color="primary" @click="saveDate">OK</v-btn>
                </v-col>
              </v-row>
            </div>
          </v-dialog>
        </v-text-field>
      </v-col>

      <v-col>
        <v-autocomplete
          v-model="uploader"
          label="Search by Uploader"
          variant="outlined"
          :items="['atul', 'vaibhav', 'harsh']"
          item-text="fullName"
          item-value="uploader"
          @input="searchByUploader"
        ></v-autocomplete>
      </v-col>
      <v-col>
        <v-btn @click="searchAll" color="primary">Search</v-btn>
      </v-col>
      <v-col>
        <v-btn @click="clearAll" color="primary">Clear Data</v-btn>
      </v-col>
    </v-row>

    <!-- Data table -->
    <v-data-table-server
      v-model:items-per-page="itemsPerPage"
      :headers="headers"
      :items-length="serverItems.length"
      :items="serverItems"
      :loading="loading"
      :search="search.email"
      item-value="name"
      :items-per-page-options="[5, 10, 15, 20, -1]"
      @update:options="loadItems"
    ></v-data-table-server>
  </v-container>
</template>
    
    <script>
import axios from "axios";
export default {
  data() {
    return {
      email: "",
      uploader: "",
      date: null,
      selectedFile: null,
      datePickerModal: false,
      itemsPerPage: 5,
      formattedDate: null,
      loading: false,
      search: "",
      excelFile: null,
      duplicatesRemoved: false,

      serverItems: [
        {
          index: 1,
          first_name: "atul",
          last_name: "kumar",
          email: "atul@example.com",
          uploader: "atul",
          uploaded_on: "2023-01-01",
        },
        {
          index: 2,
          first_name: "vaibhav",
          last_name: "Mishra",
          email: "vaibhav@example.com",
          uploader: "vaibhav",
          uploaded_on: "2023-01-01",
        },
        {
          index: 3,
          first_name: "harsh",
          last_name: "singh",
          email: "harsh@example.com",
          uploader: "harsh",
          uploaded_on: "2023-01-02",
        },
        {
          index: 4,
          first_name: "atul",
          last_name: "kumar",
          email: "atul@example.com",
          uploader: "atul",
          uploaded_on: "2023-01-01",
        },
        {
          index: 5,
          first_name: "vaibhav",
          last_name: "Mishra",
          email: "vaibhav@example.com",
          uploader: "vaibhav",
          uploaded_on: "2023-01-01",
        },
        {
          index: 6,
          first_name: "harsh",
          last_name: "singh",
          email: "harsh@example.com",
          uploader: "harsh",
          uploaded_on: "2023-01-02",
        },
      ],
      itemsPerPage: 5,
      currentPage: 1,
      headers: [
        { title: "S.No", key: "index" },
        { title: "First Name", key: "first_name" },
        { title: "Last Name", key: "last_name" },
        { title: "Email", key: "email" },
        { title: "Uploader", key: "uploader" },
        { title: "Uploaded On", key: "uploaded_on" },
      ],
      search: { email: "" },
    };
  },
  computed: {
    getUploaderItems() {
      const uploaderItems = this.serverItems.map((item) => {
        return {
          fullName: `${item.first_name} ${item.last_name}`,
          uploader: item.uploader,
        };
      });
      //   return uploaderItems;
    },
  },
  methods: {
    clearFile() {
      this.selectedFile = null;
    },
    handleFileChange(event) {
      this.file = event.target.files[0];
    },
    // Inside your methods
    async removeDuplicateAndDownload() {
      if (!this.selectedFile) {
        alert("Please select a file");
        return;
      }

      const formData = new FormData();
      formData.append("file", this.file);

      try {
        const response = await axios.post("/api/remove-download", formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
          responseType: "blob", // Set responseType to blob
        });

        const blob = new Blob([response.data], {
          type: "application/octet-stream",
        });

        const link = document.createElement("a");
        link.href = window.URL.createObjectURL(blob);
        link.download = "new_excel_file.xlsx";

        link.click();
        this.selectedFile = null;
        console.log("Ssuccessfully removed dupliates");
        this.duplicatesRemoved = true;
      } catch (error) {
        console.error("Error", error.message);
        alert("Error");
      }
    },
    downloadFile(filePath) {
      // Check if the file exists
      if (filePath) {
        const link = document.createElement("a");
        link.href = filePath;
        link.download = "new_excel_file.xlsx";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
      } else {
        console.error("File not available");
        // Handle the error or provide feedback to the user
      }
    },

    async saveData() {
      if (!this.file) {
        alert("Please select a file");
        return;
      }

      const formData = new FormData();
      formData.append("file", this.file);

      try {
        const response = await axios.post("/api/save-data", formData);

        if (response.data.error) {
          if (response.data.error === "No data to save.") {
            alert("No data to save. Please run removeDuplicates first.");
            this.duplicatesRemoved = false; 
          } else {
            alert("Error during import: " + response.data.error.error);
            this.duplicatesRemoved = false; 
          }
        } else {
          console.log(response.data.message);
          alert("File uploaded and processed successfully");
          this.duplicatesRemoved = false; 
          this.clearFile();
        }
      } catch (error) {
        console.error("Detailed error information:", error);
        this.duplicatesRemoved = false; 
        if (error.response) {
          console.error("Response status:", error.response.status);
          console.error("Response data:", error.response.data);
          alert("Server error: " + error.response.data.error);
        } else if (error.request) {
          console.error("No response received");
          alert("No response received from the server.");
        } else {
          console.error("Error setting up the request:", error.message);
          alert("Error setting up the request: " + error.message);
        }
      }
    },

    searchByEmail() {
      this.search.email = this.email.toLowerCase();
      this.search.date = "";
      this.search.uploader = "";
      this.fetchData();
    },
    fetchData() {
      const filteredData = this.serverItems.filter((item) =>
        this.matchesSearchCriteria(item)
      );
      this.serverItems = filteredData;
      this.totalItems = filteredData.length;
    },
    matchesSearchCriteria(item) {
      const emailMatch = item.email.toLowerCase().includes(this.email);
      const dateMatch = this.date ? item.uploaded_on === this.date : true;
      const uploaderMatch = item.uploader.toLowerCase().includes(this.uploader);
      return emailMatch && dateMatch && uploaderMatch;
    },
    searchByEmailRealTime() {
      console.log("Current Email:", this.email);
      this.search.email = this.email.toLowerCase();
      console.log("Updated Search Email:", this.search.email);
      this.search.date = "";
      this.search.uploader = "";
      this.fetchData();
    },
    openDatePickerModal() {
      this.datePickerModal = true;
    },

    closeDatePickerModal() {
      this.datePickerModal = false;
    },

    saveDate() {
      if (this.date) {
        this.formattedDate = this.formatDate(this.date);
        console.log("Selected Date:", this.formattedDate);
        this.search.date = this.formattedDate;
        this.searchByEmail();
      }
      this.closeDatePickerModal();
    },

    formatDate(date) {
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const day = String(date.getDate()).padStart(2, "0");
      return `${year}-${month}-${day}`;
    },

    searchByUploader() {
      console.log("Searching by Uploader...");
    },

    loadItems() {
      console.log("Loaded Items:", this.serverItems);
    },

    searchAll() {
      this.fetchData();
    },

    clearAll() {
      this.email = "";
      this.date = null;
      this.uploader = "";
      this.formattedDate = null;
      this.fetchData();
    },
  },
  // watch: {
  //   email: "searchRealTime",
  //   date: "searchRealTime",
  //   uploader: "searchRealTime",
  // },
};
</script>
    


<style lang="css" scoped>
.row-pointer:hover {
  cursor: pointer;
}

.header {
  /* text-align: center; */
}

.cutout-mode {
  margin-left: 0px !important;
  text-align: right;
}

.cutout-mode button {
  margin-right: 10px;
}
</style>