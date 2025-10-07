<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center px-4">
    <div
      class="bg-white rounded-lg border border-slate-300 p-6 w-full max-w-md"
    >
      <!-- タイトル -->
      <div class="text-center mb-8">
        <h1 class="text-lg font-semibold text-slate-900">
          {{ isSignup ? "新規ユーザー登録" : "ログイン" }}
        </h1>
      </div>

      <!-- フォーム -->
      <form @submit.prevent="onSubmit" class="space-y-4">
        <!-- メールアドレス -->
        <AuthFormField
          field-id="email"
          label="メールアドレス"
          type="email"
          placeholder="Email"
          v-model="emailValue"
          :error-message="shouldShowEmailError ? emailError : undefined"
          @blur="emailBlur"
        />

        <!-- パスワード -->
        <AuthFormField
          field-id="password"
          label="パスワード"
          type="password"
          placeholder="Password"
          v-model="passwordValue"
          :error-message="shouldShowPasswordError ? passwordError : undefined"
          @blur="passwordBlur"
        />

        <!-- ボタンセクション -->
        <div class="flex flex-col gap-2 pt-4">
          <Button
            type="submit"
            class="w-full bg-slate-900 text-white hover:bg-slate-800"
            :disabled="isSubmitting"
          >
            {{ isSignup ? "登録する" : "ログイン" }}
          </Button>
        </div>
      </form>

      <!-- 切り替えリンク -->
      <div class="text-center mt-6">
        <p class="text-sm text-gray-600">
          {{
            isSignup
              ? "既にアカウントをお持ちですか？"
              : "アカウントをお持ちでない方"
          }}
        </p>
        <button
          @click="switchAuthMode"
          type="button"
          class="text-sm text-slate-900 hover:text-slate-700 underline mt-1"
        >
          {{ isSignup ? "ログインはこちら" : "新規登録はこちら" }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { toTypedSchema } from "@vee-validate/zod";
import { useForm, useField } from "vee-validate";
import { useRouter } from "vue-router";
import { ref, computed } from "vue";
import { Button } from "@/components/ui/button";
import AuthFormField from "@/components/ui/AuthFormField.vue";
import { authSchema, type AuthFormData } from "@/lib/validation";

interface Props {
  isSignup?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  isSignup: false,
});

const emit = defineEmits<{
  submit: [data: AuthFormData];
}>();

const router = useRouter();

// vee-validate with zod
const { handleSubmit, setFieldError, isSubmitting, resetForm } = useForm({
  validationSchema: toTypedSchema(authSchema),
  initialValues: {
    email: "",
    password: "",
  },
});

// 個別フィールドの設定
const {
  value: emailValue,
  errorMessage: emailError,
  meta: emailMeta,
  handleBlur: emailBlur,
} = useField<string>("email");

const {
  value: passwordValue,
  errorMessage: passwordError,
  meta: passwordMeta,
  handleBlur: passwordBlur,
} = useField<string>("password");

// フォーム送信試行状態
const hasSubmitted = ref(false);

// フォーム送信処理
const onSubmit = handleSubmit(async (data: AuthFormData) => {
  hasSubmitted.value = true;
  try {
    emit("submit", data);
  } catch (error) {
    console.error("フォーム送信エラー:", error);
    // サーバーエラーの場合の処理
    setFieldError("email", "サーバーエラーが発生しました。");
  }
});

// エラー表示の判定
const shouldShowEmailError = computed(() => {
  return hasSubmitted.value || emailMeta.touched;
});

const shouldShowPasswordError = computed(() => {
  return hasSubmitted.value || passwordMeta.touched;
});

// 認証モード切り替え
const switchAuthMode = (): void => {
  // フォーム状態をリセット
  resetForm();
  hasSubmitted.value = false;

  if (props.isSignup) {
    router.push("/signin");
  } else {
    router.push("/signup");
  }
};
</script>
