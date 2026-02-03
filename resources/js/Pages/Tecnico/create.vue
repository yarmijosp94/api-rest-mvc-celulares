<script setup lang="ts">
import { router, useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import FormField from '../../components/FormField.vue'

const form = useForm({
  nombre: '',
  telefono: '',
  email: '',
  especialidad: '',
  certificacion: '',
  fecha_contratacion: '',
  activo: true
})

const handleSubmit = () => {
  form.post(route('tecnicos.store'), {
    preserveScroll: true,
    onError: (errors) => {
      console.error('Errores de validación:', errors)
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
      <h1 class="text-3xl font-bold">Crear Nuevo Técnico</h1>
      <button @click="handleCancel" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
        Cancelar
      </button>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
      <form @submit.prevent="handleSubmit" class="space-y-6">
        <FormField label="Nombre" name="nombre" :error="form.errors.nombre" required>
          <input
            v-model="form.nombre"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg"
            :class="{ 'border-red-500': form.errors.nombre }"
            placeholder="Nombre completo del técnico"
            required
          />
        </FormField>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <FormField label="Teléfono" name="telefono" :error="form.errors.telefono">
            <input
              v-model="form.telefono"
              type="tel"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg"
              :class="{ 'border-red-500': form.errors.telefono }"
              placeholder="Ej: 0991234567"
            />
          </FormField>

          <FormField label="Email" name="email" :error="form.errors.email">
            <input
              v-model="form.email"
              type="email"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg"
              :class="{ 'border-red-500': form.errors.email }"
              placeholder="correo@ejemplo.com"
            />
          </FormField>
        </div>

        <FormField label="Especialidad" name="especialidad" :error="form.errors.especialidad" hint="Especialidad técnica del técnico">
          <input
            v-model="form.especialidad"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg"
            :class="{ 'border-red-500': form.errors.especialidad }"
            placeholder="Ej: Reparación de pantallas, microsoldadura, etc."
          />
        </FormField>

        <FormField label="Certificación" name="certificacion" :error="form.errors.certificacion" hint="Número de certificación o licencia">
          <input
            v-model="form.certificacion"
            type="text"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg"
            :class="{ 'border-red-500': form.errors.certificacion }"
            placeholder="Ej: CERT-001234"
          />
        </FormField>

        <FormField label="Fecha de Contratación" name="fecha_contratacion" :error="form.errors.fecha_contratacion">
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
            :disabled="form.processing"
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
          >
            <span v-if="form.processing">Guardando...</span>
            <span v-else>Guardar Técnico</span>
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
