export interface User {
  id: number
  name: string
  email: string
  avatar?: AvatarProps
  status: UserStatus
  location: string
}

export type UserStatus = 'subscribed' | 'unsubscribed' | 'bounced'
export type SaleStatus = 'paid' | 'failed' | 'refunded'

export interface Mail {
  id: number
  unread?: boolean
  from: User
  subject: string
  body: string
  date: string
}

export interface Member {
  name: string
  username: string
  role: 'member' | 'owner'
  avatar: AvatarProps
}

export interface Stat {
  title: string
  icon: string
  value: number | string
  variation: number
  formatter?: (value: number) => string
}

export interface Sale {
  id: string
  date: string
  status: SaleStatus
  email: string
  amount: number
}

export interface Notification {
  id: number
  unread?: boolean
  sender: User
  body: string
  date: string
}

export type Period = 'daily' | 'weekly' | 'monthly'

export interface Range {
  start: Date
  end: Date
}

export interface Cliente {
  id: string
  tipoDocumento: string
  numeroDocumento: string
  razonSocial: string
  direccion: string
  telefono: string
  email: string
  createdAt: string
  updatedAt: string
}

export type FacturaEstado = 'emitida' | 'pagada' | 'anulada'
export type FacturaTipo = 'reparacion' | 'venta_repuesto' | 'otro'

export interface Producto {
  id: string
  codigo: string
  nombre: string
  descripcion: string | null
  precioUnitario: number
  stock: number
  categoriaId: string
  createdAt: string
  updatedAt: string
}

export interface DetalleFactura {
  productoId: string
  cantidad: number
  precioUnitario: number
  descuento: number
  subtotal: number
}

export interface Factura {
  id: string
  numeroFactura: string
  serie: string
  clienteId: string
  usuarioId: string
  ordenReparacionId?: string
  tipoFactura: FacturaTipo
  fechaEmision: string
  fechaVencimiento: string | null
  subtotal: number
  igv: number
  descuento: number
  total: number
  estado: FacturaEstado
  observaciones: string | null
  detalles?: DetalleFactura[]
  createdAt: string
  updatedAt: string
}

export interface Equipo {
  id: string
  clienteId: string
  marca: string
  modelo: string
  imei: string
  numeroSerie?: string
  color: string
  patronBloqueo?: string
  observacionesIniciales?: string
  estadoFisico: 'Excelente' | 'Bueno' | 'Regular' | 'Malo'
  accesorios?: Record<string, boolean>
  createdAt: string
  updatedAt: string
  cliente?: Cliente
}

export interface Repuesto {
  id: string
  codigo: string
  nombre: string
  descripcion?: string
  categoria: string
  marcaCompatible?: string
  modeloCompatible?: string
  stockActual: number
  stockMinimo: number
  precioCompra: number
  precioVenta: number
  proveedor?: string
  ubicacion?: string
  estado: 'disponible' | 'agotado' | 'por_pedir'
  createdAt: string
  updatedAt: string
}

export interface RepuestoConPivot extends Repuesto {
  pivot: {
    cantidad: number
    precioUnitario: number
    subtotal: number
  }
}

export interface OrdenReparacion {
  id: string
  numeroOrden: string
  clienteId: string
  equipoId: string
  tecnicoId: string
  fechaIngreso: string
  fechaPromesa?: string
  fechaEntrega?: string
  fallaReportada: string
  diagnosticoTecnico?: string
  solucionAplicada?: string
  estado: 'recibido' | 'en_diagnostico' | 'diagnosticado' | 'pendiente_aprobacion' | 'aprobado' | 'rechazado' | 'en_reparacion' | 'reparado' | 'entregado' | 'cancelado'
  prioridad: 'baja' | 'media' | 'alta' | 'urgente'
  costoManoObra: number
  costoRepuestos: number
  costoTotal: number
  adelanto?: number
  observaciones?: string
  requiereAprobacion: boolean
  aprobadoPorCliente?: boolean
  fechaAprobacion?: string
  motivoRechazo?: string
  createdAt: string
  updatedAt: string
  cliente?: Cliente
  equipo?: Equipo
  tecnico?: Tecnico
  repuestos?: RepuestoConPivot[]
}

export interface Tecnico {
  id: string
  nombre: string
  telefono?: string
  email?: string
  especialidad?: string
  certificacion?: string
  fechaContratacion?: string
  activo: boolean
  createdAt: string
  updatedAt: string
}
