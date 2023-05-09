<template>
    <div>
        <div class="modal fade" id="add-edit-dept" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="modal-title modal-title-font" id="exampleModalLabel">{{ title }}</div>
                    </div>
                    <ValidationObserver v-slot="{ handleSubmit }">
                        <form class="form-horizontal" id="form" @submit.prevent="handleSubmit(onSubmit)">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <ValidationProvider name="FarmName" mode="eager" rules="required"
                                                            v-slot="{ errors }">
                                            <div class="form-group">
                                                <label for="Farm Name">Farm Name <span class="error">*</span></label>
                                                <input type="text" class="form-control"
                                                       :class="{'error-border': errors[0]}" id="FarmName"
                                                       v-model="FarmName" name="Farm Name" placeholder="Farm Name">
                                                <span class="error-message"> {{ errors[0] }}</span>
                                            </div>
                                        </ValidationProvider>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <ValidationProvider name="Owner" mode="eager" rules="required"
                                                            v-slot="{ errors }">
                                            <div class="form-group">
                                                <label for="Owner Name">Owner Name <span class="error">*</span></label>
                                                <input type="text" class="form-control"
                                                       :class="{'error-border': errors[0]}" id="Owner"
                                                       v-model="Owner" name="FarmName" placeholder="Owner Name">
                                                <span class="error-message"> {{ errors[0] }}</span>
                                            </div>
                                        </ValidationProvider>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <ValidationProvider name="registrationNumber" mode="eager" rules="required"
                                                            v-slot="{ errors }">
                                            <div class="form-group">
                                                <label for="registrationNumber">Registration Number <span class="error">*</span></label>
                                                <input type="text" class="form-control"
                                                       :class="{'error-border': errors[0]}" id="Address"
                                                       v-model="registrationNumber" name="registrationNumber" placeholder="registrationNumber">
                                                <span class="error-message"> {{ errors[0] }}</span>
                                            </div>
                                        </ValidationProvider>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <ValidationProvider name="mobile" mode="eager" rules="required|min:11|max:11"
                                                            v-slot="{ errors }">
                                            <div class="form-group">
                                                <label for="mobile">Mobile <span class="error">*</span></label>
                                                <input type="number" class="form-control"
                                                       :class="{'error-border': errors[0]}"
                                                       v-model="mobile" placeholder="Mobile">
                                                <span class="error-message"> {{ errors[0] }}</span>
                                            </div>
                                        </ValidationProvider>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <ValidationProvider name="Address" mode="eager" rules="required"
                                                            v-slot="{ errors }">
                                            <div class="form-group">
                                                <label for="Address">Address <span class="error">*</span></label>
                                                <input type="text" class="form-control"
                                                       :class="{'error-border': errors[0]}" id="Address"
                                                       v-model="Address" name="Address" placeholder="Address">
                                                <span class="error-message"> {{ errors[0] }}</span>
                                            </div>
                                        </ValidationProvider>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <submit-form v-if="buttonShow" :name="buttonText"/>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" @click="closeModal">Close</button>
                            </div>
                        </form>
                    </ValidationObserver>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import {bus} from "../../app";
import {Common} from "../../mixins/common";
import {mapGetters} from "vuex";

export default {
    mixins: [Common],
    data() {
        return {
            title: '',
            FarmID:'',
            FarmName: '',
            Owner: '',
            registrationNumber:'',
            Address: '',
            buttonText: '',
            mobile: '',
            type: 'add',
            actionType: '',
            buttonShow: false,
            roles: [],
        }
    },
    computed: {},
    mounted() {
        $('#add-edit-dept').on('hidden.bs.modal', () => {
            this.$emit('changeStatus')
        });
        bus.$on('add-edit-farmlistData', (row) => {
            if (row) {
                let instance = this;
                this.axiosGet('admin/get-farmList-info/' + row.FarmID, function (response) {
                    console.log(row.FarmID)
                    var user = response.data;
                    instance.title = 'Update Farm';
                    instance.buttonText = "Update";
                    instance.FarmID = user.FarmID;
                    instance.FarmName = user.FarmName;
                    instance.Owner = user.Owner;
                    instance.registrationNumber = user.RegistrationNumber;
                    instance.Address = user.Address;
                    instance.mobile = user.Mobile;
                    instance.buttonShow = true;
                    instance.actionType = 'edit';
                }, function (error) {

                });
            } else {
                this.title = 'Add Farm';
                this.buttonText = "Add";
                this.FarmName = '';
                this.Owner = '';
                this.Address = '',
                    this.registrationNumber = '',
                    this.mobile = '',
                this.buttonShow = true;
                this.actionType = 'add'
            }
            $("#add-edit-dept").modal("toggle");
            // $(".error-message").html("");
        })
    },
    destroyed() {
        bus.$off('add-edit-farmlistData')
    },
    methods: {
        closeModal() {
            $("#add-edit-dept").modal("toggle");
        },
        onSubmit() {
            this.$store.commit('submitButtonLoadingStatus', true);
            let url = '';
            if (this.actionType === 'add') url = 'admin/add-farm-list-data';
            else url = 'admin/update-farm-list-data'
            this.axiosPost(url, {
                FarmID: this.FarmID,
                FarmName: this.FarmName,
                Owner: this.Owner,
                mobile: this.mobile,
                Address: this.Address,
                registrationNumber: this.registrationNumber,

            }, (response) => {
                this.successNoti(response.message);
                $("#add-edit-dept").modal("toggle");
                bus.$emit('refresh-datatable');
                this.$store.commit('submitButtonLoadingStatus', false);
            }, (error) => {
                this.errorNoti(error);
                this.$store.commit('submitButtonLoadingStatus', false);
            })
        }
    }
}
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
