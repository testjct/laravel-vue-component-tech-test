<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <b>Products</b>
            <b-button
              @click="OpenAddModal()"
              class="float-right"
              variant="outline-primary"
              >Add New</b-button
            >
          </div>

          <div class="card-body">
            <b-table
              responsive
              hover
              :fields="fields"
              :items="items"
              id="myTable"
            >
              <template #cell(image)="data">
                <img width="100" :src="'/files/' + data.item.image" />
              </template>
              <template #cell(action)="data">
                <b-button
                  class="mb-1"
                  type="button"
                  @click="EditProduct(data.item.id)"
                  variant="outline-primary"
                  >Edit</b-button
                >
                <b-button
                  class="mb-1"
                  type="button"
                  @click="DeleteProduct(data.item.id)"
                  variant="danger"
                  >Delete</b-button
                >
              </template>
            </b-table>
          </div>
        </div>

        <!-- Add modal -->
        <b-modal
          id="modal-1"
          title="Add Product"
          :hide-footer="true"
          ref="add-modal"
        >
          <b-form
            ref="form"
            @submit="onSubmit"
            @reset="onReset"
            enctype="multipart/form-data"
          >
            <b-form-group
              id="input-group-2"
              label-for="input-2"
              class="has-validation"
            >
              <b-label>Name <span class="text-danger">*</span></b-label>
              <b-form-input
                id="input-2"
                v-model="form.name"
                placeholder="Enter name"
                name="name"
              ></b-form-input>
              <div class="invalid-feedback d-block" v-if="error.name != ''">
                {{ error.name }}
              </div>
            </b-form-group>

            <b-form-group
              id="input-group-3"
              label-for="input-3"
            >
              <b-label>Manufactured Year <span class="text-danger">*</span></b-label>
              <b-form-select
                id="input-3"
                v-model="form.year"
                :options="years"
                name="year"
              ></b-form-select>
              <div class="invalid-feedback d-block" v-if="error.year != ''">
                {{ error.year }}
              </div>
            </b-form-group>

            <b-form-group
              id="input-group-2"
              label-for="input-2"
            >
              <b-label>Image <span class="text-danger">*</span></b-label>
              <b-form-file
                v-model="form.image"
                placeholder="Choose a file"
                accept="image/*"
                name="image"
                @change="onFileChange"
              ></b-form-file>
              <div class="invalid-feedback d-block" v-if="error.image != ''">
                {{ error.image }}
              </div>
            </b-form-group>
            <div class="row mb-2" v-if="form.preview_image != ''">
              <div class="col-6">
                <img width="150" :src="form.preview_image" />
              </div>
            </div>

            <b-button type="submit" variant="primary">Submit</b-button>
            <b-button type="reset" variant="danger">Reset</b-button>
          </b-form>
        </b-modal>

        <!-- Edit modal -->
        <b-modal
          id="modal-2"
          title="Edit Product"
          :hide-footer="true"
          ref="edit-modal"
        >
          <b-form
            ref="formEdit"
            @submit="onEditSubmit"
            enctype="multipart/form-data"
          >
            <b-form-group
              id="input-group-2"
              label-for="input-2"
              class="has-validation"
            >
              <b-label>Name <span class="text-danger">*</span></b-label>
              <b-form-input
                id="input-2"
                v-model="form.name"
                placeholder="Enter name"
                name="name"
              ></b-form-input>
              <div class="invalid-feedback d-block" v-if="error.name != ''">
                {{ error.name }}
              </div>
            </b-form-group>

            <b-form-group
              id="input-group-3"
              label-for="input-3"
            >
              <b-label>Manufactured Year <span class="text-danger">*</span></b-label>
              <b-form-select
                id="input-3"
                v-model="form.year"
                :options="years"
                name="year"
              ></b-form-select>
              <div class="invalid-feedback d-block" v-if="error.year != ''">
                {{ error.year }}
              </div>
            </b-form-group>

            <b-form-group
              id="input-group-2"
              label-for="input-2"
            >
              <b-label>Image <span class="text-danger">*</span></b-label>
              <b-form-file
                v-model="form.image"
                placeholder="Choose a file"
                accept="image/*"
                name="image"
                @change="onFileChange"
              ></b-form-file>
              <div class="invalid-feedback d-block" v-if="error.image != ''">
                {{ error.image }}
              </div>
            </b-form-group>
            <div class="row mb-2" v-if="form.preview_image != ''">
              <div class="col-6">
                <img width="150" :src="form.preview_image" />
              </div>
            </div>
            <b-button type="submit" variant="primary">Update</b-button>
            <b-button type="button" @click="CloseModal()" variant="danger"
              >Cancel</b-button
            >
          </b-form>
        </b-modal>
      </div>
    </div>
  </div>
</template>

<script>
// Import jquery
import "jquery/dist/jquery.min.js";
import "datatables.net-dt/js/dataTables.dataTables";
import "datatables.net-dt/css/jquery.dataTables.min.css";
import $ from "jquery";
// Sweet alert
import VueSweetalert2 from "vue-sweetalert2";

// If you don't need the styles, do not connect
import "sweetalert2/dist/sweetalert2.min.css";
Vue.use(VueSweetalert2);

export default {
  data() {
    return {
      // Data to be passed to component
      items: [],
      perPage: ["10", "25", "50"],

      fields: [
        { key: "id", label: "ID", tdClass: "align-middle" },
        { key: "name", label: "Name", tdClass: "align-middle" },
        {
          key: "manufactured_year",
          label: "Manufactured Year",
          tdClass: "align-middle",
        },
        { key: "image", label: "Image", tdClass: "align-middle" },
        { key: "action", label: "Actions", tdClass: "align-middle" },
      ],
      years: [],
      form: {
        id: 0,
        name: "",
        year: "",
        image: null,
      },

      error: {
        name: "",
        year: "",
        image: "",
      },

      is_edit : false,
      old_image: ''
    };
  },

  // Once load component then get list of data
  created() {
    this.getData();
    const year = new Date().getFullYear();
    for (let index = 1990; index <= year; index++) {
      this.years.push({ value: index, text: index });
    }
  },

  methods: {
    //   Get list method
    getData() {
      axios.get(`/products/get_all`).then((response) => {
        $("#myTable").DataTable().destroy();
        this.items = response.data;
        setTimeout(function () {
          $("#myTable").DataTable();
        }, 1000);
      });
    },

    //  Form reset method
    onReset() {
      this.form = {
        id: 0,
        name: "",
        year: "",
        image: null,
        preview_image: "",
      };

      this.error = {
        name: "",
        year: "",
        image: "",
      };
    },

    // Add product method
    onSubmit(event) {
      event.preventDefault();

      this.error = {
        name: "",
        year: "",
        image: "",
      };

      var frmData = new FormData(this.$refs.form);
      axios
        .post(`/products`, frmData)
        .then((response) => {
          this.$refs["add-modal"].hide();
          this.getData();
          this.$swal("Success!", response.data.message, "success");
        })
        .catch((error) => {
          if (error.response.status == 422) {
            var err_data = error.response.data.errors;
            for (const property in err_data) {
              if (property == "name") {
                this.error.name = err_data[property][0];
              }

              if (property == "image") {
                this.error.image = err_data[property][0];
              }

              if (property == "year") {
                this.error.year = err_data[property][0];
              }
            }
          } else {
            this.$swal.fire("Error!", "Internal server error!", "error");
          }
        });
    },

    // Delete product method
    DeleteProduct(id) {
      this.$swal({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
      }).then((result) => {
        if (result.isConfirmed) {
          axios
            .delete(`/products/${id}`)
            .then((response) => {
              this.getData();
              this.$swal.fire(
                "Deleted!",
                "Product deleted successfully.",
                "success"
              );
            })
            .catch((error) => {
              this.$swal.fire("Error!", "Product is safe.", "error");
            });
        }
      });
    },

    // Edit product view model
    EditProduct(id) {
      this.error = {
        name: "",
        year: "",
        image: "",
      };
      const found = this.items.find((product) => product.id == id);
      if (found) {
        this.is_edit = true;
        this.old_image = "/files/" + found.image

        this.form = {
          id: id,
          name: found.name,
          year: found.manufactured_year,
          image: null,
          preview_image: "/files/" + found.image,
        };
        this.$refs["edit-modal"].show();
      } else {
        this.$swal.fire("Error!", "Product not found.", "error");
      }
    },

    // Edit form submit
    onEditSubmit(event) {
      event.preventDefault();

      this.error = {
        name: "",
        year: "",
        image: "",
      };

      var frmData = new FormData(this.$refs.formEdit);
      axios
        .post(`/product/update/${this.form.id}`, frmData)
        .then((response) => {
          this.$refs["edit-modal"].hide();
          this.getData();
          this.$swal("Success!", response.data.message, "success");
          this.is_edit = false;
          this.old_image = ''
        })
        .catch((error) => {
          if (error.response.status == 422) {
            var err_data = error.response.data.errors;
            for (const property in err_data) {
              if (property == "name") {
                this.error.name = err_data[property][0];
              }

              if (property == "image") {
                this.error.image = err_data[property][0];
              }

              if (property == "year") {
                this.error.year = err_data[property][0];
              }
            }
          } else {
            this.$swal.fire("Error!", "Internal server error!", "error");
          }
        });
    },

    // Close edit model method
    CloseModal() {
      this.error = {
        name: "",
        year: "",
        image: "",
      };
      this.is_edit = false;
      this.old_image = ''

      this.$refs["edit-modal"].hide();
      this.form = {
        id: 0,
        name: "",
        year: "",
        image: null,
        preview_image: "",
      };
    },

    // Open new product model
    OpenAddModal() {
      this.error = {
        name: "",
        year: "",
        image: "",
      };
      this.form = {
        id: 0,
        name: "",
        year: "",
        image: null,
        preview_image: "",
      };
      this.$refs["add-modal"].show();
    },

    // Preview Image
    onFileChange(e) {
      if (e.target.value.length == 0) {
        if(this.is_edit == true){
          this.form.preview_image = this.old_image;
        } else {
          this.form.preview_image = '';
        }
      } else {
        const file = e.target.files[0];
        this.form.preview_image = URL.createObjectURL(file);
      }
    },
  },
};
</script>
