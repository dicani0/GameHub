<script setup lang="ts">
/**
 * This component demonstrates how to use the custom color palette
 * with classes like bg-primary-500 that automatically change based on the theme.
 */
</script>

<template>
  <div class="space-y-4">
    <h2 class="text-xl font-bold">Color Palette Example</h2>
    <p class="text-sm text-muted-foreground">
      This example shows how to use the custom color palette with classes like bg-primary-500
      that automatically change based on the theme (cyan for light theme, purple for dark theme).
    </p>
    
    <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-5">
      <!-- Primary color shades -->
      <div class="flex flex-col items-center">
        <div class="h-16 w-16 rounded-md bg-primary-50"></div>
        <span class="mt-1 text-xs">bg-primary-50</span>
      </div>
      <div class="flex flex-col items-center">
        <div class="h-16 w-16 rounded-md bg-primary-100"></div>
        <span class="mt-1 text-xs">bg-primary-100</span>
      </div>
      <div class="flex flex-col items-center">
        <div class="h-16 w-16 rounded-md bg-primary-200"></div>
        <span class="mt-1 text-xs">bg-primary-200</span>
      </div>
      <div class="flex flex-col items-center">
        <div class="h-16 w-16 rounded-md bg-primary-300"></div>
        <span class="mt-1 text-xs">bg-primary-300</span>
      </div>
      <div class="flex flex-col items-center">
        <div class="h-16 w-16 rounded-md bg-primary-400"></div>
        <span class="mt-1 text-xs">bg-primary-400</span>
      </div>
      <div class="flex flex-col items-center">
        <div class="h-16 w-16 rounded-md bg-primary-500"></div>
        <span class="mt-1 text-xs">bg-primary-500</span>
      </div>
      <div class="flex flex-col items-center">
        <div class="h-16 w-16 rounded-md bg-primary-600"></div>
        <span class="mt-1 text-xs">bg-primary-600</span>
      </div>
      <div class="flex flex-col items-center">
        <div class="h-16 w-16 rounded-md bg-primary-700"></div>
        <span class="mt-1 text-xs">bg-primary-700</span>
      </div>
      <div class="flex flex-col items-center">
        <div class="h-16 w-16 rounded-md bg-primary-800"></div>
        <span class="mt-1 text-xs">bg-primary-800</span>
      </div>
      <div class="flex flex-col items-center">
        <div class="h-16 w-16 rounded-md bg-primary-900"></div>
        <span class="mt-1 text-xs">bg-primary-900</span>
      </div>
      <div class="flex flex-col items-center">
        <div class="h-16 w-16 rounded-md bg-primary-950"></div>
        <span class="mt-1 text-xs">bg-primary-950</span>
      </div>
    </div>
    
    <div class="mt-8">
      <p class="text-sm">
        These colors will automatically change based on the theme:
      </p>
      <ul class="ml-6 mt-2 list-disc text-sm">
        <li>Light theme: Cyan colors (hue 180)</li>
        <li>Dark theme: Purple colors (hue 270)</li>
      </ul>
    </div>
  </div>
</template>