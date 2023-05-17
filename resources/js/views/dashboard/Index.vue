<template>
    <div>
        <div class="page-title-box">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h4 class="page-title">Dashboard</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active">Welcome to LA Monitoring Admin Dashboard</li>
                    </ol>
                </div>
                <div class="col-sm-6">
                    <div class="float-right d-none d-md-block">
                        <div class="dropdown">
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="#">Action</a> <a class="dropdown-item" href="#">Another
                                action</a> <a class="dropdown-item" href="#">Something else here</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#">Separated link</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card mini-stat bg-primary text-white">
                    <div class="card-body">
                        <div class="mb-4">
                            <div class="float-left mini-stat-img mr-4"><img src="assets/images/services-icon/01.png"
                                                                            alt=""/></div>
                            <h5 class="font-16 text-uppercase mt-0 text-white-50">Total Farms</h5>
                            <h4 class="font-500">{{ totalFarms }} <i class="mdi mdi-arrow-up text-success ml-2"></i>
                            </h4>
                        </div>
                        <div class="pt-2">
                            <div class="float-right">
                                <a href="#" class="text-white-50"><i class="mdi mdi-arrow-right h5"></i></a>
                            </div>
                            <p class="text-white-50 mb-0">Since last month</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card mini-stat bg-primary text-white">
                    <div class="card-body">
                        <div class="mb-4">
                            <div class="float-left mini-stat-img mr-4"><img src="assets/images/services-icon/02.png"
                                                                            alt=""/></div>
                            <h5 class="font-16 text-uppercase mt-0 text-white-50">Total Breeding</h5>
                            <h4 class="font-500">{{ totalBreeding }}<i class="mdi mdi-arrow-down text-danger ml-2"></i>
                            </h4>
                        </div>
                        <div class="pt-2">
                            <div class="float-right">
                                <a href="#" class="text-white-50"><i class="mdi mdi-arrow-right h5"></i></a>
                            </div>
                            <p class="text-white-50 mb-0">Since last month</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card mini-stat bg-primary text-white">
                    <div class="card-body">
                        <div class="mb-4">
                            <div class="float-left mini-stat-img mr-4"><img src="assets/images/services-icon/03.png"
                                                                            alt=""/></div>
                            <h5 class="font-16 text-uppercase mt-0 text-white-50">Total ReBreeding</h5>
                            <h4 class="font-500">{{ totalReBreeding }} <i
                                class="mdi mdi-arrow-up text-success ml-2"></i></h4>
                        </div>
                        <div class="pt-2">
                            <div class="float-right">
                                <a href="#" class="text-white-50"><i class="mdi mdi-arrow-right h5"></i></a>
                            </div>
                            <p class="text-white-50 mb-0">Since last month</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card mini-stat bg-primary text-white">
                    <div class="card-body">
                        <div class="mb-4">
                            <div class="float-left mini-stat-img mr-4"><img src="assets/images/services-icon/04.png"
                                                                            alt=""/></div>
                            <h5 class="font-16 text-uppercase mt-0 text-white-50">Total LA</h5>
                            <h4 class="font-500">{{ totalLa }} <i class="mdi mdi-arrow-up text-success ml-2"></i></h4>
                        </div>
                        <div class="pt-2">
                            <div class="float-right">
                                <a href="#" class="text-white-50"><i class="mdi mdi-arrow-right h5"></i></a>
                            </div>
                            <p class="text-white-50 mb-0">Since last month</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row eventTitle">
            <div class="col-md-2">
                <h4 style="margin:0"> Events : </h4>
            </div>
            <div class="col-md-10">
                <marquee direction="left">
                    <span class="marqueClass" v-for="(event,index) in events" :key="index">
                        <-----------{{ event.EventName }} ----------->
                    </span>
                </marquee>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4" style="padding: 15px 0">
                <div class="row">
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div v-if="this.events.length===0"> <p>No Events for Display</p></div>
                        <div v-else>
                            <b-carousel
                                id="carousel-1"
                                v-model="slide"
                                :interval="4000"
                                controls
                                indicators
                                background="#ababab"
                                img-width="1024px"
                                img-height="480px"
                                style="text-shadow: 1px 1px 2px #333;"
                                @sliding-start="onSlideStart"
                                @sliding-end="onSlideEnd"
                            >
                                <!-- Text slides with image -->
                                <b-carousel-slide v-for="(singleEventImg,index) in events" :key="index"
                                                  :caption="`${singleEventImg.EventName}`"
                                                  :img-src="`${image}/uploads/${singleEventImg.EventImage}`"
                                ></b-carousel-slide>

                            </b-carousel>
                            <!--                                <p class="mt-4">-->
                            <!--                                    Slide #: {{ slide }}<br>-->
                            <!--                                    Sliding: {{ sliding }}-->
                            <!--                                </p>-->
                        </div>
                    </div>


                </div>
                <div class="col-md-4"></div>
            </div>
        </div>

        <div class="row NoticeTitle">
            <div class="col-md-2">
                <h4 style="margin:0"> Notice : </h4>
            </div>
            <div class="col-md-10">
                <div v-if="this.notices.length===0"> <p>No Events for Display</p></div>
                <div v-else>
                    <marquee  direction="left">
                    <span class="marqueClass" v-for="(notice,index) in notices" :key="index">
                        <-----------{{ notice.NoticeTitle }} ----------->
                    </span>
                    </marquee>
                </div>

            </div>
        </div>
    </div>
</template>
<script>
import {Common} from "../../mixins/common";
import {baseurl} from "../../base_url";

export default {
    mixins: [Common],
    data() {
        return {
            events: [],
            notices: [],
            totalFarms: '',
            totalLa: '',
            totalBreeding: '',
            totalReBreeding: '',
            isLoading: true,
            image: '',
            slide: 0,
            sliding: null
        }
    },
    computed: {
        me() {
            return this.$store.state.me
        }
    },
    created() {
        this.getdashBoardData();
        this.image = baseurl;
    },
    methods: {
        getdashBoardData() {
            this.axiosGet('dashboard-data', (response) => {
                console.log(response)
                this.events = response.events
                this.notices = response.notices
                this.totalFarms = response.totalFarms
                this.totalLa = response.totalLa
                this.totalBreeding = response.totalBreeding
                this.totalReBreeding = response.totalReBreeding
                // this.events.forEach(element =>
                //     setTimeout(function() {
                //         console.log(element)
                //         document.getElementById("marquee").textContent = element.EventName;
                //     }, 500)
                //
                // );
                // this.notices.forEach(element2 =>
                //     setTimeout(function() {
                //         console.log(element2)
                //         document.getElementById("marquee2").textContent = element2.NoticeDescription;
                //     }, 500)
                //
                // );
                this.isLoading = false;
                }, (error) => {
                  this.errorNoti(error);
            });
        },
        onSlideStart(slide) {
            console.log(slide)
            this.sliding = true
        },
        onSlideEnd(slide) {
            this.sliding = false
        }
    }
}
</script>


<style scoped>

.marqueClass{
    margin: 14px;
    font-size: 20px;
    font-weight: bold
}
.eventTitle {
    background: #0faea3a1;
}

.NoticeTitle {
    background: #28a74596;
}

.bg-blue {
    background: #626ed4 !important;
    text-align: center;
    text-transform: uppercase;
}

.card-body {
    padding: 0.60rem !important;
}

.bg-blue span {
    font-size: 18px;
}

.task {
    background: #00a55d2b;
    padding: 5px 8px;
    font-size: 14px;
    margin-bottom: 10px;
    border-radius: 5px;
    font-weight: bold;
}

.links {
    /*height: 148px;*/
}

.helpline {
    height: 120px;
    text-align: center;
    position: relative;
    background-image: linear-gradient(146deg, #626ed4, #626ed4);
}

.helpline span {
    position: absolute;
    top: 40%;
    left: 0;
    right: 0;
    font-weight: bold;
    font-size: 20px;
    text-transform: uppercase;
    color: #FFFFFF;
}

.contact {
    height: 120px;
    text-align: center;
    padding-top: 20px;
    background-image: linear-gradient(146deg, #626ed4, #626ed4);
}

.contact p {
    margin: 0;
    padding: 2px;
    font-weight: bold;
    color: #ffffff;
}

.header-bg {
    text-align: center;
    font-size: 16px;
    font-weight: bold;
    text-transform: uppercase;
}
#carousel-1 img {
    width: 1024px !important;
    height: 100vh !important;
}
</style>
