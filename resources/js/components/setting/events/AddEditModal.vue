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
                                        <ValidationProvider name="EventName" mode="eager" rules="required"
                                                            v-slot="{ errors }">
                                            <div class="form-group">
                                                <label for="EventName">Event Name <span class="error">*</span></label>
                                                <input type="text" class="form-control"
                                                       :class="{'error-border': errors[0]}" id="BullName"
                                                       v-model="EventName" name="EventName" placeholder="Event  Name">
                                                <span class="error-message"> {{ errors[0] }}</span>
                                            </div>
                                        </ValidationProvider>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <ValidationProvider name="EventPeriod" mode="eager" rules="required"
                                                            v-slot="{ errors }">
                                            <div class="form-group">
                                                <label for="EventName">Event Period <span class="error">*</span></label>

                                                <date-picker v-model="EventPeriod" name="EventPeriod"   class="form-control" :class="{'error-border': errors[0]}"
                                                             format="DD-MM-YYYY" range valueType="format"></date-picker>
<!--                                                <input type="text" class="form-control"-->
<!--                                                       :class="{'error-border': errors[0]}" id="BullName"-->
<!--                                                       v-model="EventPeriod" name="EventName" placeholder="Event  Period">-->
                                                <span class="error-message"> {{ errors[0] }}</span>
                                            </div>
                                        </ValidationProvider>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-12">
                                            <ValidationProvider name="EventImage" mode="eager" rules="required"
                                                                v-slot="{ errors }">
                                                <div class="form-group">
                                                    <label for="EventImage">Bull Code <span class="error">*</span></label>
                                                    <input type="file" class="form-control" id="EventImage" @change="imgUpload($event,index)">
                                                    <span class="error-message"> {{ errors[0] }}</span>
                                                </div>
                                            </ValidationProvider>
                                        </div>
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
import {bus} from "../../../app";
import {Common} from "../../../mixins/common";
import {mapGetters} from "vuex";

export default {
    mixins: [Common],
    data() {
        return {
            title: '',
            EventID:'',
            EventName:'',
            EventPeriod:[],
            attachment: '',
            buttonText: '',
            type: 'add',
            actionType: '',
            buttonShow: false,
        }
    },
    computed: {},
    mounted() {
        $('#add-edit-dept').on('hidden.bs.modal', () => {
            this.$emit('changeStatus')
        });
        bus.$on('add-edit-bullListData', (row) => {
            if (row) {
                let instance = this;
                this.axiosGet('admin/breeding/get-bull-info/' + row.BullID, function (response) {
                    var user = response.data;
                    console.log(row.BullID)
                    instance.title = 'Update Bull Type';
                    instance.buttonText = "Update";
                    instance.BullID = user.BullID;
                    instance.BullTypeID = user.BullTypeID;
                    instance.BullTypeName = user.BullTypeName;
                    instance.BullName = user.BullName;
                    instance.BullCode = user.BullCode;
                    instance.buttonShow = true;
                    instance.actionType = 'edit';
                    instance.getData();
                }, function (error) {
                    console.log(error)
                });
            } else {
                this.title = 'Add Bull Type';
                this.buttonText = "Add";
                this.BullTypeName = '';
                this.buttonShow = true;
                this.actionType = 'add'
                this.getData();
            }
            $("#add-edit-dept").modal("toggle");
            // $(".error-message").html("");
        })
    },
    destroyed() {
        bus.$off('add-edit-bullListData')
    },
    methods: {
        imgUpload(e,index) {
            var input = e.target
            var file = input.files[0]
            if (file.type !== 'image/png' && file.type !== 'image/jpeg') {
                this.errorNoti('Invalid file type for Advance ID')
            } else {
                if (file.size > 5000000) {
                    this.errorNoti('Maximum file size 5 MB for Advance ID '+this.fields[index].advanceId + '!')
                } else {
                    this.processImage(file,index)
                }
            }
        },
        processImage(file,index) {
            let instance = this;
            var reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function () {
               this.attachment = reader.result
                console.log(this.attachment)
            };
            reader.onerror = function (error) {
                console.log('Error: ', error);
            };
        },
        closeModal() {
            $("#add-edit-dept").modal("toggle");
        },
        getData() {
            let instance = this;
            this.axiosGet('admin/bull/modal', function (response) {
                instance.BullType = response.BullType;
                console.log(instance.BullType)
            }, function (error) {

            });
        },
        onSubmit() {
            this.$store.commit('submitButtonLoadingStatus', true);
            let url = '';
            if (this.actionType === 'add') url = 'admin/breeding/add-bull-list-data';
            else url = 'admin/update-bull-list-data'
            this.axiosPost(url, {
                BullID: this.BullID,
                BullTypeID: this.BullTypeID,
                BullName: this.BullName,
                BullCode: this.BullCode,
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
