<template>
  <div class="container safari_only">
    <App-Header title="Profile" />
    <div class="container-scroll">
      <div class="profile-avatar">
        <VanImage width="100" height="100" :src="avatarUrl" />
      </div>
      <div class="profile-forms">
        <div class="form-label">First Name</div>
        <van-field v-model.trim="form.firstName" />
        <div class="form-label">Last Name</div>
        <van-field v-model.trim="form.lastName" />
        <div class="form-label">E-mail</div>
        <van-field v-model.trim="form.email" />
        <div class="form-label">Date of Birth</div>
        <van-field readonly v-model="form.birth">
          <template #right-icon>
            <van-icon color="#31452e" size="36" name="calendar-o" />
          </template>
        </van-field>
        <div class="form-label">Gender</div>
        <div class="genders">
          <van-radio-group
            checked-color="#31452e"
            icon-size="32px"
            direction="horizontal"
            v-model="form.gender"
          >
            <van-radio name="male">
              <template #default>
                <div class="gender-label">Male</div>
              </template>
            </van-radio>
            <van-radio name="female">
              <template #default>
                <div class="gender-label">Female</div>
              </template>
            </van-radio>
          </van-radio-group>
        </div>
        <div class="form-label">Location</div>
        <van-field v-model.trim="form.location" rows="3" autosize type="textarea" />
      </div>
    </div>
    <div class="container-bottom">
      <van-button :disabled="notSave" round block color="#000000"> Save Changes </van-button>
    </div>
  </div>
</template>

<script setup>
import AppHeader from '@/components/CustomNavBar/index.vue'
const avatarUrl = new URL('@/assets/image/avatar.png', import.meta.url).href
const form = ref({
  firstName: undefined,
  lastName: undefined,
  email: undefined,
  birth: undefined,
  gender: 'male',
  location: undefined
})
const notSave = computed(() => {
  return !form.value.firstName || !form.value.lastName
})
</script>
<style lang="scss" scoped>
@import url('@/assets/style/gj.scss');
.container {
  display: flex;
  flex-direction: column;
  overflow-y: hidden;
  .container-scroll {
    height: calc(100vh - 134px);
    .profile-avatar {
      padding-top: 1em;
      text-align: center;
    }
    .profile-forms {
      .form-label {
        padding-top: 16px;
        padding-bottom: 8px;
        font-size: 14px;
        font-weight: 600;
        letter-spacing: 0.07px;
        color: rgba(120, 130, 138, 1);
      }
      ::v-deep(.van-cell) {
        padding: 0;
      }
      ::v-deep(.van-field__control) {
        padding: 10px;
        border-radius: 15px;
        border: 1px solid #31452e;
      }
      .genders {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        .gender-label {
          padding-left: 10px;
          font-size: 20px;
        }
        ::v-deep(.van-radio) {
          padding: 12px 30px 12px 12px;
          border-radius: 15px;
          border: 1px solid #31452e;
        }
      }
    }
  }
}
</style>
