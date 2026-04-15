/**
 * ============================================
 * VALIDACIONES.JS - Sistema Control Patrimonial
 * Validaciones de coherencia centralizadas
 * ============================================
 */

const Validador = {

    // ======= REGLAS DE VALIDACIÓN =======

    /** Verifica que un campo no esté vacío */
    requerido(valor) {
        return valor !== null && valor !== undefined && valor.toString().trim().length > 0;
    },

    /** Longitud mínima */
    longitudMinima(valor, min) {
        return valor.trim().length >= min;
    },

    /** Longitud máxima */
    longitudMaxima(valor, max) {
        return valor.trim().length <= max;
    },

    /** Formato de email válido */
    emailValido(valor) {
        const regex = /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/;
        return regex.test(valor.trim());
    },

    /** Solo letras, espacios, tildes y ñ */
    soloLetras(valor) {
        const regex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/;
        return regex.test(valor.trim());
    },

    /** Solo alfanuméricos, guiones y puntos (para códigos) */
    codigoValido(valor) {
        const regex = /^[a-zA-Z0-9\-_.]+$/;
        return regex.test(valor.trim());
    },

    /** Solo letras, números, espacios y caracteres comunes */
    textoGeneral(valor) {
        const regex = /^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑüÜ\s.,;:()\"'\-\/]+$/;
        return regex.test(valor.trim());
    },

    /** Contraseña con al menos 1 letra y 1 número */
    contrasenaSegura(valor) {
        const tieneLetra = /[a-zA-Z]/.test(valor);
        const tieneNumero = /[0-9]/.test(valor);
        return tieneLetra && tieneNumero;
    },

    /** Fecha no futura */
    fechaNoFutura(valor) {
        if (!valor) return false;
        const fecha = new Date(valor + 'T23:59:59');
        const hoy = new Date();
        return fecha <= hoy;
    },

    /** Fecha inicio <= Fecha fin */
    rangoFechasValido(fechaInicio, fechaFin) {
        if (!fechaInicio || !fechaFin) return true; // si alguna falta, no validar rango
        return new Date(fechaInicio) <= new Date(fechaFin);
    },

    /** Dos valores NO deben ser iguales (ej: persona origen ≠ destino) */
    valoresDiferentes(val1, val2) {
        if (!val1 || !val2) return true; // si alguno falta, no comparar
        return val1.toString() !== val2.toString();
    },

    // ======= FEEDBACK VISUAL =======

    /**
     * Marcar un campo como válido
     */
    marcarValido(campo, mensaje = '') {
        campo.classList.remove('is-invalid');
        campo.classList.add('is-valid');
        this._actualizarFeedback(campo, 'valid-feedback', mensaje);
    },

    /**
     * Marcar un campo como inválido
     */
    marcarInvalido(campo, mensaje) {
        campo.classList.remove('is-valid');
        campo.classList.add('is-invalid');
        this._actualizarFeedback(campo, 'invalid-feedback', mensaje);
    },

    /**
     * Limpiar estado de validación
     */
    limpiar(campo) {
        campo.classList.remove('is-valid', 'is-invalid');
        this._removerFeedback(campo);
    },

    /**
     * Actualizar o crear elemento de feedback
     */
    _actualizarFeedback(campo, clase, mensaje) {
        // Buscar en el padre inmediato del campo
        let parent = campo.parentElement;
        // Si está dentro de un input-group, subir un nivel
        if (parent.classList.contains('input-group')) {
            parent = parent.parentElement;
        }

        let feedback = parent.querySelector('.' + clase);
        if (!feedback && mensaje) {
            feedback = document.createElement('div');
            feedback.className = clase;
            parent.appendChild(feedback);
        }
        if (feedback) {
            feedback.textContent = mensaje;
            feedback.style.display = mensaje ? 'block' : 'none';
        }

        // Ocultar el feedback del tipo contrario
        const otraClase = clase === 'valid-feedback' ? 'invalid-feedback' : 'valid-feedback';
        const otro = parent.querySelector('.' + otraClase);
        if (otro) otro.style.display = 'none';
    },

    /**
     * Remover ambos feedbacks
     */
    _removerFeedback(campo) {
        let parent = campo.parentElement;
        if (parent.classList.contains('input-group')) {
            parent = parent.parentElement;
        }
        const feedbacks = parent.querySelectorAll('.valid-feedback, .invalid-feedback');
        feedbacks.forEach(f => f.style.display = 'none');
    },

    // ======= VALIDACIÓN DE FORMULARIO COMPLETO =======

    /**
     * Validar un formulario completo con reglas definidas
     * @param {HTMLFormElement} form - El formulario
     * @param {Object} reglas - Objeto con reglas por campo
     * @returns {boolean} - true si todo es válido
     * 
     * Formato de reglas:
     * {
     *   'nombreCampo': [
     *     { validar: (valor) => boolean, mensaje: 'Texto error' }
     *   ]
     * }
     */
    validarFormulario(form, reglas) {
        let todosValidos = true;

        for (const [nombreCampo, validaciones] of Object.entries(reglas)) {
            const campo = form.querySelector(`[name="${nombreCampo}"]`);
            if (!campo) continue;

            const valor = campo.value;
            let campoValido = true;

            for (const regla of validaciones) {
                if (!regla.validar(valor, form)) {
                    this.marcarInvalido(campo, regla.mensaje);
                    campoValido = false;
                    todosValidos = false;
                    break; // solo mostrar primer error
                }
            }

            if (campoValido) {
                // Solo marcar válido si el campo tiene valor
                if (valor && valor.trim().length > 0) {
                    this.marcarValido(campo);
                } else {
                    this.limpiar(campo);
                }
            }
        }

        return todosValidos;
    },

    /**
     * Adjuntar validación en tiempo real a un campo
     */
    adjuntarValidacionCampo(campo, validaciones) {
        const self = this;

        const validar = () => {
            const valor = campo.value;
            for (const regla of validaciones) {
                if (!regla.validar(valor)) {
                    self.marcarInvalido(campo, regla.mensaje);
                    return false;
                }
            }
            if (valor && valor.trim().length > 0) {
                self.marcarValido(campo);
            } else {
                self.limpiar(campo);
            }
            return true;
        };

        campo.addEventListener('blur', validar);
        campo.addEventListener('input', () => {
            // En input, solo limpiar error si ya lo había
            if (campo.classList.contains('is-invalid')) {
                validar();
            }
        });

        return validar;
    },

    /**
     * Mostrar contador de caracteres
     */
    agregarContador(campo, max) {
        let parent = campo.parentElement;
        let contador = parent.querySelector('.char-counter');
        if (!contador) {
            contador = document.createElement('small');
            contador.className = 'char-counter text-muted d-block mt-1';
            parent.appendChild(contador);
        }

        const actualizar = () => {
            const len = campo.value.length;
            contador.textContent = `${len}/${max} caracteres`;
            if (len > max) {
                contador.classList.remove('text-muted');
                contador.classList.add('text-danger');
            } else {
                contador.classList.remove('text-danger');
                contador.classList.add('text-muted');
            }
        };

        campo.addEventListener('input', actualizar);
        actualizar(); // Inicializar
    }
};
