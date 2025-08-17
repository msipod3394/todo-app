import { z, ZodError } from "zod";

export const taskSchema = z.object({
  name: z
    .string()
    .min(1, "タスク名は必須です")
    .max(100, "タスク名は100文字以内で入力してください")
    .refine((val) => val.trim().length > 0, {
      message: "タスク名を入力してください",
    }),
  dueDate: z.union([z.string(), z.date(), z.null()]).optional(),
});

export type TaskFormData = z.infer<typeof taskSchema>;

export interface ValidationResult {
  success: boolean;
  errors: Record<string, string>;
}

export const validateTask = (task: TaskFormData): ValidationResult => {
  try {
    taskSchema.parse(task);
    return { success: true, errors: {} };
  } catch (error) {
    const errors: Record<string, string> = {};
    if (error instanceof ZodError) {
      error.errors.forEach((err) => {
        if (err.path[0]) {
          errors[err.path[0] as string] = err.message;
        }
      });
    }
    return { success: false, errors };
  }
};

export const useTaskValidation = () => {
  return {
    taskSchema,
    validateTask,
  };
};
