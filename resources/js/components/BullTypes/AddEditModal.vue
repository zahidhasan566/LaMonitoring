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
                                        <ValidationProvider name="BullTypeName" mode="eager" rules="required"
                                                            v-slot="{ errors }">
                                            <div class="form-group">
                                                <label for="BullTypeName">Bull Type Name <span class="error">*</span></label>
                                                <input type="text" class="form-control"
                                                       :class="{'error-border': errors[0]}" id="BullTypeName"
                                                       v-model="BullTypeName" name="BullTypeName" placeholder="Bull Type Name">
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
            BullTypeID:'',
            BullTypeName: '',
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
        bus.$on('add-edit-bullTypeListData', (row) => {
            if (row) {
                let instance = this;
                this.axiosGet('admin/breeding/get-bull-type-info/' + row.BullTypeID, function (response) {
                    var user = response.data;
                    console.log(user)
                    instance.title = 'Update Bull Type';
                    instance.buttonText = "Update";
                    instance.BullTypeID = user.BullTypeID;
                    instance.BullTypeName = user.BullTypeName;
                    instance.buttonShow = true;
                    instance.actionType = 'edit';
                }, function (error) {
                    console.log(error)
                });
            } else {
                this.title = 'Add Bull Type';
                this.buttonText = "Add";
                this.BullTypeName = '';
                this.buttonShow = true;
                this.actionType = 'add'
            }
            $("#add-edit-dept").modal("toggle");
            // $(".error-message").html("");
        })
    },
    destroyed() {
        bus.$off('add-edit-bullTypeListData')
    },
    methods: {
        closeModal() {
            $("#add-edit-dept").modal("toggle");
        },
        onSubmit() {
            this.$store.commit('submitButtonLoadingStatus', true);
            let url = '';
            if (this.actionType === 'add') url = 'admin/breeding/add-bull-type-list-data';
            else url = 'admin/update-bull-type-list-data'
            this.axiosPost(url, {
                BullTypeID: this.BullTypeID,
                BullTypeName: this.BullTypeName,
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
