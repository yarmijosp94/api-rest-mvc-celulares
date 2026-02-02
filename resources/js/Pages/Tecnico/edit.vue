<script setup lang="ts">
import { ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import type { Tecnico } from '../../types'
import FormField from '../../components/FormField.vue'

const props = defineProps<{
  tecnico: Tecnico
  usuarios: Array<{
    id: string
    name: string
    email: string
  }>
}>()

const form = useForm({
  especialidad: props.tecnico.especialidad || '',
  certificacion: props.tecnico.certificacion || '',
  fecha_contratacion: props.tecnico.fechaContratacion || '',
  activo: props.tecnico.activo ?? true
})

const isSubmitting = ref(false)

const handleSubmit = () => {
  form.put(route('tecnicos.update', props.tecnico.id), {
    onSuccess: () => {
      router.visit(route('tecnicos.index'))
    }
  })
}

const handleCancel = () => {
  router.get(route('tecnicos.index'))
}
</script>

<template>
  <div class="space-y-6 p-6">
    <div class="flex justify-between items-center">
      <h1 class="text-3xl font-bold">Editar Tecnico</h1>
      <button @click="handleCancel" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
        Volver
      </button>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
      <div class="mb-6 p-4 bg-gray-50 rounded-lg">
        <div class="grid grid-cols-2 gap-4">
          <div>
            <span class="text-sm text-gray-500">ID:</span>
            <span class="font-medium">{{ tecnico.id }}</span>
          </div>
          <div>
            <span class="text-sm text-gray-500">Usuario:</span>
            <span class="font-medium">{{ tecnico.user?.name || '-' }}</span>
          </div>
          <div>
            <span class="text-sm text-gray-500">Email:</span>
            <span class="font-medium">{{ tecnico.user?.email || '-' }}</span>
          </div>
          <div>
            <span class="text-sm text-gray-500">Creado:</span>
            <span class="font-medium">{{ tecnico.createdAt }}</span>
          </div>
        </div>
      </div>

      <form @submit.prevent="handleSubmit" class="space-y-6">
        <FormField label="Especialidad" name="especialidad" :error="form.errors.especialidad" hint="Especialidad tecnica del tecnico">
          <input
            v-model="form.especialidad"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg"
            :class="{ 'border-red-500': form.errors.especialidad }"
            placeholder="Ej: Reparacion de pantallas, microsoldadura, etc."
          />
        </FormField>

        <FormField label="Certificacion" name="certificacion" :error="form.errors.certificacion" hint="Numero de certificacion o licencia">
          <input
            v-model="form.certificacion"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg"
            :class="{ 'border-red-500': form.errors.certificacion }"
            placeholder="Ej: CERT-001234"
          />
        </FormField>

        <FormField label="Fecha de Contratacion" name="fecha_contratacion" :error="form.errors.fecha_contratacion">
          <input
            v-model="form.fecha_contratacion"
            type="date"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg"
            :class="{ 'border-red-500': form.errors.fecha_contratacion }"
          />
        </FormField>

        <FormField label="Estado" name="activo" :error="form.errors.activo">
          <label class="flex items-center gap-2">
            <input
              v-model="form.activo"
              type="checkbox"
              class="w-5 h-5 border-gray-300 rounded"
            />
            <span>Activo</span>
          </label>
        </FormField>

        <div class="flex items-center gap-3 pt-4">
          <button
            type="submit"
            :disabled="isSubmitting"
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
          >
            <span v-if="isSubmitting">Guardando...</span>
            <span v-else>Actualizar Tecnico</span>
          </button>
          <button
            type="button"
            @click="handleCancel"
            class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300"
          >
            Cancelar
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
