export interface UsuarioForm {
    usuario_id: string;
    nombre: string;
    ap_pat: string;
    ap_mat: string;
    curp: string;
    email: string;
    telefono: string;
    celular: string;
    fecha_nacimiento: string;
    oficio: string;
    estado_civil_id: string;
    cat_genero_id: string;
    cat_rol_id: string;
    nombre_contacto: string;
    apellido_contacto: string;
    telefono_contacto: string;
    celular_contacto: string;
    parentesco: string;
    tipo_sangre: string;
    alergias: string;
    medicamentos: string;
    estatura: string;
    complexion_id: string;
    seguro_social: number;
    tipo_seguro_id: string;
    numero_seguro: string;
    condiciones_preexistentes: string;
    url_foto: string | null
  }