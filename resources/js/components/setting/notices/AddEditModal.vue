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
                                    <div class="col-12 col-md-12">
                                        <ValidationProvider name="NoticeTitle" mode="eager" rules="required"
                                                            v-slot="{ errors }">
                                            <div class="form-group">
                                                <label for="NoticeTitle">Notice Title <span class="error">*</span></label>
                                                <input type="text" class="form-control"
                                                       :class="{'error-border': errors[0]}" id="NoticeTitle"
                                                       v-model="NoticeTitle" name="NoticeTitle" placeholder="Notice  Title">
                                                <span class="error-message"> {{ errors[0] }}</span>
                                            </div>
                                        </ValidationProvider>
                                    </div>
                                    <div class="col-12 col-md-6">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <ValidationProvider name="NoticeStartFrom" mode="eager" rules="required"
                                                            v-slot="{ errors }">
                                            <div class="form-group">
                                                <label for="NoticeStartFrom">Notice Start Date Time<span
                                                    class="error">*</span></label>

                                                <datetime format="YYYY-MM-DD H:i:s" width="300px"
                                                          v-model="NoticeStartFrom"></datetime>
                                                <span class="error-message"> {{ errors[0] }}</span>
                                            </div>
                                        </ValidationProvider>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <ValidationProvider name="NoticeEndTo" mode="eager" rules="required"
                                                            v-slot="{ errors }">
                                            <div class="form-group">
                                                <label for="NoticeEndTo">Notice End Date Time<span
                                                    class="error">*</span></label>

                                                <datetime format="YYYY-MM-DD H:i:s" width="300px"
                                                          v-model="NoticeEndTo"></datetime>
                                                <span class="error-message"> {{ errors[0] }}</span>
                                            </div>
                                        </ValidationProvider>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <ValidationProvider name="NoticeDescription" mode="eager" rules="required"
                                                            v-slot="{ errors }">
                                        <div class="form-group">
                                            <label for="Notice Description">Notice Description <span class="error">*</span></label>
                                            <textarea class="form-control" v-model="NoticeDescription"></textarea>
                                            <span class="error-message"> {{ errors[0] }}</span>
                                        </div>
                                        </ValidationProvider>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <ValidationProvider name="Status" mode="eager" rules="required"
                                                            v-slot="{ errors }">
                                            <div class="form-group">
                                                <label for="status">Status <span class="error">*</span></label>
                                                <select class="form-control" v-model="Status">
                                                    <option value="1">Active</option>
                                                    <option value="0">Inactive</option>
                                                </select>
                                                <span class="error-message"> {{ errors[0] }}</span>
                                            </div>
                                        </ValidationProvider>
                                    </div>

                                </div>
                            </div>
                            <div class="modal-footer">
                                <submit-form v-if="buttonShow" :name="buttonText"/>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                        @click="closeModal">Close
                                </button>
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
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';
import {mapGetters} from "vuex";
import datetime from 'vuejs-datetimepicker';


export default {
    components: {DatePicker, datetime},
    mixins: [Common],
    data() {
        return {
            title: '',
            NoticeID: '',
            NoticeTitle: '',
            NoticeStartFrom: '',
            NoticeEndTo: '',
            NoticeDescription: '',
            Status: '',
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
        bus.$on('add-edit-noticeData', (row) => {
            if (row) {
                let instance = this;
                instance.axiosGet('admin/setting/noticeList/get-notice-list-info/' + row.NoticeID, function (response) {
                    var user = response.data;
                    instance.title = 'Update Notice';
                    instance.buttonText = "Update";
                    instance.NoticeID = user.NoticeID;
                    instance.NoticeTitle = user.NoticeTitle;
                    instance.NoticeStartFrom = user.NoticeStartFrom;
                    instance.NoticeEndTo = user.NoticeEndTo;
                    instance.NoticeDescription = user.NoticeDescription;
                    instance.Status = user.Status;
                    instance.buttonShow = true;
                    instance.actionType = 'edit';
                }, function (error) {
                    console.log(error)
                });
            } else {
                this.title = 'Add Notice';
                this.buttonText = "Add";
                this.NoticeID = '';
                this.NoticeTitle = '';
                this.NoticeStartFrom = '';
                this.NoticeEndTo = '';
                this.NoticeDescription = '';
                this.Status = '';
                this.buttonShow = true;
                this.actionType = 'add'

            }
            $("#add-edit-dept").modal("toggle");
            // $(".error-message").html("");
        })
    },
    destroyed() {
        bus.$off('add-edit-noticeData')
    },
    methods: {
        closeModal() {
            $("#add-edit-dept").modal("toggle");
        },
        onSubmit() {
            console.log(this)
            this.$store.commit('submitButtonLoadingStatus', true);
            let url = '';
            if (this.actionType === 'add') url = 'admin/setting/noticeList/add-notice-list-data';
            else url = 'admin/update/setting/noticeList/add-notice-list-data'
            this.axiosPost(url, {
                NoticeID: this.NoticeID,
                NoticeTitle: this.NoticeTitle,
                NoticeStartFrom: this.NoticeStartFrom,
                NoticeEndTo: this.NoticeEndTo,
                NoticeDescription: this.NoticeDescription,
                Status: this.Status,
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
<style lang="scss">

.mx-datepicker {
    width: 100% !important;
}

.datepicker .picker .picker-content {
    width: 350px !important;
}
</style>

