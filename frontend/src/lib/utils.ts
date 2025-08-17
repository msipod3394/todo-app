import { type ClassValue, clsx } from "clsx";
import { twMerge } from "tailwind-merge";
import { type Ref } from "vue";

export function cn(...inputs: ClassValue[]): string {
  return twMerge(clsx(inputs));
}

export function valueUpdater<T>(
  updaterOrValue: T | ((value: T) => T),
  ref: Ref<T>
): void {
  ref.value =
    typeof updaterOrValue === "function"
      ? (updaterOrValue as (value: T) => T)(ref.value)
      : updaterOrValue;
}
