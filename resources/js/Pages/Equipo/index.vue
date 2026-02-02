<script setup lang="ts">
import { ref, computed, h, resolveComponent } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import type { TableColumn } from '@nuxt/ui'
import { upperFirst } from 'scule'
import { getPaginationRowModel, getFilteredRowModel } from '@tanstack/table-core'
import type { Row } from '@tanstack/table-core'
import type { Equipo } from '../../types'

const UAvatar = resolveComponent('UAvatar')
const UButton = resolveComponent('UButton')
const UDropdownMenu = resolveComponent('UDropdownMenu')
const UCheckbox = resolveComponent('UCheckbox')
const UModal = resolveComponent('UModal')
const UBadge = resolveComponent('UBadge')

const props = defineProps<{
  equipos: {
    data: Equipo[]
    links: any[]
    meta: any
  }
  stats?: {
    total: number
    excelente: number
    bueno: number
    regular: number
    malo: number
  }
}>()

const toast = useToast()
const table = useTemplateRef('table')

const columnFilters = ref<{ id: string; value: string }[]>([])
const globalFilter = ref('')
const columnVisibility = ref()
const rowSelection = ref({})

const status = ref<'idle' | 'pending' | 'success' | 'error'>('success')
const isDeleteModalOpen = ref(false)
const equipoToDelete = ref<Equipo | null>(null)

const handleCreate = () => {
  router.visit(route('equipos.create'))
}

const handleEdit = (equipo: Equipo) => {
  router.visit(route('equipos.edit', equipo.id))
}

const confirmDelete = (equipo: Equipo) => {
  equipoToDelete.value = equipo
  isDeleteModalOpen.value = true
}

const handleDelete = () => {
  if (!equipoToDelete.value) return

  router.delete(route('equipos.destroy', equipoToDelete.value.id), {
    preserveScroll: true,
    onSuccess: () => {
      isDeleteModalOpen.value = false
      equipoToDelete.value = null
    }
  })
}

function getRowItems(row: Row<Equipo>) {
  return [
    {
      type: 'label',
      label: 'Acciones'
    },
    {
      label: 'Copiar ID de equipo',
      icon: 'i-lucide-copy',
      onSelect() {
        navigator.clipboard.writeText(row.original.id.toString())
        toast.add({
          title: 'Copiado al portapapeles',
          description: 'ID del equipo copiado'
        })
      }
    },
    {
      type: 'separator'
    },
    {
      label: 'Editar equipo',
      icon: 'i-lucide-pencil',
      onSelect() {
        handleEdit(row.original)
      }
    },
    {
      type: 'separator'
    },
    {
      label: 'Eliminar equipo',
      icon: 'i-lucide-trash',
      color: 'error',
      onSelect() {
        confirmDelete(row.original)
      }
    }
  ]
}

const getEstadoColor = (estado: string) => {
  switch (estado) {
    case 'Excelente':
      return 'success'
    case 'Bueno':
      return 'primary'
    case 'Regular':
      return 'warning'
    case 'Malo':
      return 'error'
    default:
      return 'neutral'
  }
}

const columns: TableColumn<Equipo>[] = [
  {
    id: 'select',
    header: ({ table }) =>
      h(UCheckbox, {
        'modelValue': table.getIsSomePageRowsSelected()
          ? 'indeterminate'
          : table.getIsAllPageRowsSelected(),
        'onUpdate:modelValue': (value: boolean | 'indeterminate') =>
          table.toggleAllPageRowsSelected(!!value),
        'ariaLabel': 'Seleccionar todos'
      }),
    cell: ({ row }) =>
      h(UCheckbox, {
        'modelValue': row.getIsSelected(),
        'onUpdate:modelValue': (value: boolean | 'indeterminate') => row.toggleSelected(!!value),
        'ariaLabel': 'Seleccionar fila'
      })
  },
  {
    accessorKey: 'marca',
    header: 'Marca / Modelo',
    cell: ({ row }) => {
      return h('div', { class: 'flex items-center gap-3' }, [
        h(UAvatar, {
          src: `https://ui-avatars.com/api/?name=${encodeURIComponent(row.original.marca + ' ' + row.original.modelo)}&background=random`,
          alt: row.original.marca,
          size: 'lg'
        }),
        h('div', undefined, [
          h('p', { class: 'font-medium text-highlighted' }, `${row.original.marca} ${row.original.modelo}`),
          h('p', { class: 'text-sm text-muted' }, row.original.imei)
        ])
      ])
    }
  },
  {
    accessorKey: 'color',
    header: 'Color',
    cell: ({ row }) => row.original.color
  },
  {
    accessorKey: 'estadoFisico',
    header: 'Estado',
    cell: ({ row }) => {
      return h(UBadge, {
        color: getEstadoColor(row.original.estadoFisico),
        label: row.original.estadoFisico
      })
    }
  },
  {
    id: 'actions',
    cell: ({ row }) => {
      return h(
        'div',
        { class: 'text-right' },
        h(
          UDropdownMenu,
          {
            content: {
              align: 'end'
            },
            items: getRowItems(row)
          },
          () =>
            h(UButton, {
              icon: 'i-lucide-ellipsis-vertical',
              color: 'neutral',
              variant: 'ghost',
              class: 'ml-auto'
            })
        )
      )
    }
  }
]

const search = computed({
  get: (): string => {
    return globalFilter.value
  },
  set: (value: string) => {
    globalFilter.value = value
  }
})

const pagination = ref({
  pageIndex: 0,
  pageSize: 10
})
</script>

<template>
  <UDashboardPanel id="equipos">
    <template #header>
      <UDashboardNavbar title="Equipos">
        <template #leading>
          <UDashboardSidebarCollapse />
        </template>

        <template #right>
          <UButton
            label="Nuevo Equipo"
            color="primary"
            icon="i-lucide-plus"
            @click="handleCreate"
          />
        </template>
      </UDashboardNavbar>
    </template>

    <template #body>
      <div class="flex flex-wrap items-center justify-between gap-1.5">
        <UInput
          v-model="search"
          class="w-full sm:w-1/2"
          size="xl"
          icon="i-lucide-search"
          placeholder="Buscar equipo por marca, modelo o IMEI..."
        />

        <div class="flex flex-wrap items-center gap-1.5">
          <UButton
            v-if="table?.tableApi?.getFilteredSelectedRowModel().rows.length"
            label="Eliminar"
            color="error"
            variant="subtle"
            icon="i-lucide-trash"
          >
            <template #trailing>
              <UKbd>
                {{ table?.tableApi?.getFilteredSelectedRowModel().rows.length }}
              </UKbd>
            </template>
          </UButton>

          <UDropdownMenu
            :items="
              table?.tableApi
                ?.getAllColumns()
                .filter((column: any) => column.getCanHide())
                .map((column: any) => ({
                  label: upperFirst(column.id),
                  type: 'checkbox' as const,
                  checked: column.getIsVisible(),
                  onUpdateChecked(checked: boolean) {
                    table?.tableApi?.getColumn(column.id)?.toggleVisibility(!!checked)
                  },
                  onSelect(e?: Event) {
                    e?.preventDefault()
                  }
                }))
            "
            :content="{ align: 'end' }"
          >
            <UButton
              label="Mostrar"
              color="neutral"
              variant="outline"
              trailing-icon="i-lucide-settings-2"
            />
          </UDropdownMenu>
        </div>
      </div>

      <UTable
        ref="table"
        v-model:column-filters="columnFilters"
        v-model:column-visibility="columnVisibility"
        v-model:row-selection="rowSelection"
        v-model:pagination="pagination"
        v-model:global-filter="globalFilter"
        :pagination-options="{
          getPaginationRowModel: getPaginationRowModel()
        }"
        :filter-options="{
          getFilteredRowModel: getFilteredRowModel()
        }"
        class="shrink-0"
        :data="equipos.data"
        :columns="columns"
        :loading="status === 'pending'"
        :ui="{
          base: 'table-fixed border-separate border-spacing-0',
          thead: '[&>tr]:bg-elevated/50 [&>tr]:after:content-none',
          tbody: '[&>tr]:last:[&>td]:border-b-0',
          th: 'py-2 first:rounded-l-lg last:rounded-r-lg border-y border-default first:border-l last:border-r',
          td: 'border-b border-default',
          separator: 'h-0'
        }"
      />

      <div class="flex items-center justify-between gap-3 border-t border-default pt-4 mt-auto">
        <div class="text-sm text-muted">
          {{ table?.tableApi?.getFilteredSelectedRowModel().rows.length || 0 }} de
          {{ table?.tableApi?.getFilteredRowModel().rows.length || 0 }} fila(s) seleccionada(s).
        </div>

        <div class="flex items-center gap-1.5">
          <UPagination
            :default-page="(table?.tableApi?.getState().pagination.pageIndex || 0) + 1"
            :items-per-page="table?.tableApi?.getState().pagination.pageSize"
            :total="table?.tableApi?.getFilteredRowModel().rows.length"
            @update:page="(p: number) => table?.tableApi?.setPageIndex(p - 1)"
          />
        </div>
      </div>
    </template>
  </UDashboardPanel>

  <UModal
    v-model:open="isDeleteModalOpen"
    title="Confirmar eliminación"
    :description="`¿Está seguro de que desea eliminar el equipo ${equipoToDelete?.marca} ${equipoToDelete?.modelo}? Esta acción no se puede deshacer.`"
  >
    <template #footer>
      <div class="flex justify-end gap-3">
        <UButton
          label="Eliminar"
          color="primary"
          variant="solid"
          icon="i-lucide-trash"
          @click="handleDelete"
        />
        <UButton
          label="Cancelar"
          color="neutral"
          variant="subtle"
          @click="isDeleteModalOpen = false"
        />
      </div>
    </template>
  </UModal>
</template>
