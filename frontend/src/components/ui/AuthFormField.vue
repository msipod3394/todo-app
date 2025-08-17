<template>
  <div class="space-y-2">
    <!-- ラベル -->
    <Label :for="fieldId" class="text-sm font-medium text-black">
      {{ label }}
    </Label>

    <!-- 入力フィールド -->
    <Input
      :id="fieldId"
      v-model="modelValue"
      :type="type"
      :placeholder="placeholder"
      :class="['h-10', hasError && 'border-red-600']"
      @blur="onBlur"
    />

    <!-- エラーメッセージ -->
    <div v-if="hasError" class="text-red-600 text-sm">
      {{ errorMessage }}
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";

interface Props {
  fieldId: string;
  label: string;
  type?: "text" | "email" | "password";
  placeholder?: string;
  modelValue: string;
  errorMessage?: string;
}

const props = withDefaults(defineProps<Props>(), {
  type: "text",
  placeholder: "",
});

const emit = defineEmits<{
  "update:modelValue": [value: string];
  blur: [];
}>();

const hasError = computed(() => !!props.errorMessage);

const onBlur = (): void => {
  emit("blur");
};

// v-model用
const modelValue = computed({
  get: () => props.modelValue,
  set: (value: string) => emit("update:modelValue", value),
});
</script>
