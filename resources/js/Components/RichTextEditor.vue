<template>
    <div class="rich-text-editor">
        <textarea :id="editorId" ref="editorRef"></textarea>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch, nextTick } from 'vue';

const props = defineProps({
    modelValue: {
        type: String,
        default: ''
    },
    editorId: {
        type: String,
        default: () => `editor-${Math.random().toString(36).substr(2, 9)}`
    }
});

const emit = defineEmits(['update:modelValue', 'content-change']);
const editorRef = ref(null);
let editorInstance = null;
let isUpdating = false;
let isInitializing = false;

onMounted(async () => {
    await nextTick();
    
    // Wait for CKEditor to be available
    const checkCKEditor = () => {
        if (window.CKEDITOR && editorRef.value) {
            initEditor();
        } else {
            setTimeout(checkCKEditor, 100);
        }
    };
    
    checkCKEditor();
});

const initEditor = () => {
    if (window.CKEDITOR && editorRef.value && !editorInstance) {
        editorInstance = window.CKEDITOR.replace(editorRef.value.id, {
            toolbar: [
                { name: 'document', items: ['Source'] },
                { name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'] },
                { name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll'] },
                { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat'] },
                { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl'] },
                { name: 'links', items: ['Link', 'Unlink', 'Anchor'] },
                { name: 'insert', items: ['Image', 'Table', 'HorizontalRule', 'SpecialChar', 'PageBreak'] },
                { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
                { name: 'colors', items: ['TextColor', 'BGColor'] },
                { name: 'tools', items: ['Maximize', 'ShowBlocks'] }
            ],
            height: 300,
            language: 'en',
            removePlugins: 'elementspath',
            resize_enabled: true
        });

        // Set initial content without triggering change event
        isInitializing = true;
        if (props.modelValue) {
            editorInstance.setData(props.modelValue);
        }

        // Listen for changes to emit content-change event (but NOT update:modelValue)
        // This allows parent to save content without triggering form updates
        editorInstance.on('change', () => {
            if (!isUpdating && !isInitializing) {
                const data = editorInstance.getData();
                // Emit content-change event for parent to save, but NOT update:modelValue
                emit('content-change', data);
            }
        });

        editorInstance.on('instanceReady', () => {
            isInitializing = true;
            if (props.modelValue) {
                editorInstance.setData(props.modelValue, { callback: () => {
                    // Data set, no action needed
                }});
            }
            // Mark initialization as complete
            setTimeout(() => {
                isInitializing = false;
            }, 500);
        });
        
        // Mark initialization as complete after a short delay
        setTimeout(() => {
            isInitializing = false;
        }, 500);
    }
};

watch(() => props.modelValue, (newValue) => {
    if (editorInstance && editorInstance.getData() !== newValue) {
        // Silently update editor content without emitting events
        isUpdating = true;
        isInitializing = true;
        editorInstance.setData(newValue || '', { callback: () => {
            // Data updated silently
        }});
        setTimeout(() => {
            isUpdating = false;
            isInitializing = false;
        }, 300);
    }
}, { flush: 'post' });

onUnmounted(() => {
    if (editorInstance) {
        try {
            editorInstance.destroy();
        } catch (e) {
            console.warn('Error destroying CKEditor:', e);
        }
        editorInstance = null;
    }
});
</script>

<style scoped>
.rich-text-editor :deep(.cke_chrome) {
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
}

.rich-text-editor :deep(.cke_top) {
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
}

.rich-text-editor :deep(.cke_contents) {
    background: white;
}
</style>

