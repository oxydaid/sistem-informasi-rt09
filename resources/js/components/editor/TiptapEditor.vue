<script setup lang="ts">
import { ref, watch, onBeforeUnmount } from 'vue';
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Table from '@tiptap/extension-table';
import TableRow from '@tiptap/extension-table-row';
import TableCell from '@tiptap/extension-table-cell';
import TableHeader from '@tiptap/extension-table-header';
import Image from '@tiptap/extension-image';
import Link from '@tiptap/extension-link';
import Underline from '@tiptap/extension-underline';
import TextAlign from '@tiptap/extension-text-align';
import Placeholder from '@tiptap/extension-placeholder';
import {
    AlignLeft, AlignCenter, AlignRight, AlignJustify,
    List, ListOrdered, Quote, Code,
    Table as TableIcon, Image as ImageIcon, Link as LinkIcon,
    Undo, Redo, Plus, Trash2, ChevronDown, ToggleLeft, Columns, Rows
} from 'lucide-vue-next';

interface Props {
    modelValue: any; // JSON or HTML
    placeholder?: string;
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: null,
    placeholder: 'Tuliskan isi konten, berita, agenda, atau halaman di sini...',
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: any): void;
}>();

const isUploading = ref(false);
const showTableMenu = ref(false);
const fileInputRef = ref<HTMLInputElement | null>(null);

const editor = useEditor({
    content: props.modelValue,
    extensions: [
        StarterKit.configure({
            heading: {
                levels: [2, 3, 4],
            },
        }),
        Underline,
        Link.configure({
            openOnClick: false,
            HTMLAttributes: {
                class: 'text-[#003087] underline hover:text-[#009CDE]',
                target: '_blank',
            },
        }),
        TextAlign.configure({
            types: ['heading', 'paragraph'],
        }),
        Image.configure({
            HTMLAttributes: {
                class: 'rounded-xl max-w-full my-4 shadow-level-1',
            },
        }),
        Table.configure({
            resizable: true,
            HTMLAttributes: {
                class: 'table-tiptap',
            },
        }),
        TableRow,
        TableHeader,
        TableCell,
        Placeholder.configure({
            placeholder: props.placeholder,
        }),
    ],
    editorProps: {
        attributes: {
            class: 'prose-tiptap min-h-[320px] p-5 focus:outline-none bg-white text-[#1A1A2E]',
        },
        handleDrop: (view, event, slice, moved) => {
            if (!moved && event.dataTransfer && event.dataTransfer.files && event.dataTransfer.files[0]) {
                const file = event.dataTransfer.files[0];
                if (file.type.startsWith('image/')) {
                    event.preventDefault();
                    uploadAndInsertImage(file);
                    return true;
                }
            }
            return false;
        },
        handlePaste: (view, event) => {
            const items = event.clipboardData?.items;
            if (items) {
                for (let i = 0; i < items.length; i++) {
                    if (items[i].type.indexOf('image') !== -1) {
                        const file = items[i].getAsFile();
                        if (file) {
                            event.preventDefault();
                            uploadAndInsertImage(file);
                            return true;
                        }
                    }
                }
            }
            return false;
        },
    },
    onUpdate: () => {
        if (!editor.value) return;
        emit('update:modelValue', editor.value.getJSON());
    },
});

watch(
    () => props.modelValue,
    (value) => {
        if (!editor.value) return;
        const isSame = JSON.stringify(editor.value.getJSON()) === JSON.stringify(value);
        if (!isSame && value) {
            editor.value.commands.setContent(value, { emitUpdate: false });
        }
    }
);

onBeforeUnmount(() => {
    editor.value?.destroy();
});

// Upload image helper
const uploadAndInsertImage = async (file: File) => {
    if (!editor.value) return;
    isUploading.value = true;
    try {
        const formData = new FormData();
        formData.append('file', file);

        const csrfToken = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content;
        const response = await fetch('/admin/contents/editor-media', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken || '',
            },
        });

        const data = await response.json();
        if (data.success && data.url) {
            editor.value.chain().focus().setImage({
                src: data.url,
                alt: data.name,
                title: data.name,
            }).run();
        }
    } catch (e) {
        console.error('Error uploading image to editor', e);
        alert('Gagal mengunggah gambar. Pastikan format file sesuai.');
    } finally {
        isUploading.value = false;
    }
};

const handleFileInputChange = (event: Event) => {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files[0]) {
        uploadAndInsertImage(input.files[0]);
    }
    input.value = '';
};

const setLink = () => {
    if (!editor.value) return;
    const previousUrl = editor.value.getAttributes('link').href;
    const url = window.prompt('Masukkan URL tautan:', previousUrl);

    if (url === null) return;
    if (url === '') {
        editor.value.chain().focus().extendMarkRange('link').unsetLink().run();
        return;
    }

    editor.value.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
};

const insertTable = () => {
    editor.value?.chain().focus().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run();
    showTableMenu.value = false;
};
</script>

<template>
    <div class="w-full border border-gray-200 rounded-2xl overflow-hidden bg-white shadow-xs flex flex-col focus-within:border-[#003087] focus-within:ring-1 focus-within:ring-[#003087] transition-all">
        <!-- Hidden file input for image upload -->
        <input
            ref="fileInputRef"
            type="file"
            accept="image/*"
            class="hidden"
            @change="handleFileInputChange"
        />

        <!-- Laravel Filament Style Minimalist Clean Toolbar -->
        <div class="bg-white border-b border-gray-200 px-3 py-2 flex flex-wrap items-center justify-between gap-1.5 select-none">
            <!-- Formatting Tools -->
            <div class="flex flex-wrap items-center gap-1">
                <!-- Bold -->
                <button
                    type="button"
                    @click="editor?.chain().focus().toggleBold().run()"
                    class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-sm transition-colors cursor-pointer"
                    :class="editor?.isActive('bold')
                        ? 'bg-amber-50 text-amber-700 border border-amber-300 font-black'
                        : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
                    title="Tebal (Ctrl+B)"
                >
                    B
                </button>

                <!-- Italic -->
                <button
                    type="button"
                    @click="editor?.chain().focus().toggleItalic().run()"
                    class="w-8 h-8 rounded-lg flex items-center justify-center italic text-sm transition-colors cursor-pointer font-serif"
                    :class="editor?.isActive('italic')
                        ? 'bg-amber-50 text-amber-700 border border-amber-300 font-bold'
                        : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
                    title="Miring (Ctrl+I)"
                >
                    I
                </button>

                <!-- Underline -->
                <button
                    type="button"
                    @click="editor?.chain().focus().toggleUnderline().run()"
                    class="w-8 h-8 rounded-lg flex items-center justify-center underline text-sm transition-colors cursor-pointer"
                    :class="editor?.isActive('underline')
                        ? 'bg-amber-50 text-amber-700 border border-amber-300 font-bold'
                        : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
                    title="Garis Bawah (Ctrl+U)"
                >
                    U
                </button>

                <!-- Strikethrough -->
                <button
                    type="button"
                    @click="editor?.chain().focus().toggleStrike().run()"
                    class="w-8 h-8 rounded-lg flex items-center justify-center line-through text-sm transition-colors cursor-pointer"
                    :class="editor?.isActive('strike')
                        ? 'bg-amber-50 text-amber-700 border border-amber-300 font-bold'
                        : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
                    title="Coretan"
                >
                    S
                </button>

                <!-- Divider -->
                <div class="w-px h-5 bg-gray-200 mx-1" />

                <!-- Link -->
                <button
                    type="button"
                    @click="setLink"
                    class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors cursor-pointer"
                    :class="editor?.isActive('link')
                        ? 'bg-amber-50 text-amber-700 border border-amber-300'
                        : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
                    title="Sisipkan Tautan (Link)"
                >
                    <LinkIcon class="w-4 h-4" />
                </button>

                <!-- H2 -->
                <button
                    type="button"
                    @click="editor?.chain().focus().toggleHeading({ level: 2 }).run()"
                    class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold transition-colors cursor-pointer"
                    :class="editor?.isActive('heading', { level: 2 })
                        ? 'bg-amber-50 text-amber-700 border border-amber-300 font-extrabold'
                        : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
                    title="Sub Judul (H2)"
                >
                    H2
                </button>

                <!-- H3 -->
                <button
                    type="button"
                    @click="editor?.chain().focus().toggleHeading({ level: 3 }).run()"
                    class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold transition-colors cursor-pointer"
                    :class="editor?.isActive('heading', { level: 3 })
                        ? 'bg-amber-50 text-amber-700 border border-amber-300 font-extrabold'
                        : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
                    title="Section (H3)"
                >
                    H3
                </button>

                <!-- Divider -->
                <div class="w-px h-5 bg-gray-200 mx-1" />

                <!-- Align Left -->
                <button
                    type="button"
                    @click="editor?.chain().focus().setTextAlign('left').run()"
                    class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors cursor-pointer"
                    :class="editor?.isActive({ textAlign: 'left' })
                        ? 'bg-amber-50 text-amber-700 border border-amber-300'
                        : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
                    title="Rata Kiri"
                >
                    <AlignLeft class="w-4 h-4" />
                </button>

                <!-- Align Center -->
                <button
                    type="button"
                    @click="editor?.chain().focus().setTextAlign('center').run()"
                    class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors cursor-pointer"
                    :class="editor?.isActive({ textAlign: 'center' })
                        ? 'bg-amber-50 text-amber-700 border border-amber-300'
                        : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
                    title="Rata Tengah"
                >
                    <AlignCenter class="w-4 h-4" />
                </button>

                <!-- Align Right -->
                <button
                    type="button"
                    @click="editor?.chain().focus().setTextAlign('right').run()"
                    class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors cursor-pointer"
                    :class="editor?.isActive({ textAlign: 'right' })
                        ? 'bg-amber-50 text-amber-700 border border-amber-300'
                        : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
                    title="Rata Kanan"
                >
                    <AlignRight class="w-4 h-4" />
                </button>

                <!-- Divider -->
                <div class="w-px h-5 bg-gray-200 mx-1" />

                <!-- Blockquote -->
                <button
                    type="button"
                    @click="editor?.chain().focus().toggleBlockquote().run()"
                    class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors cursor-pointer"
                    :class="editor?.isActive('blockquote')
                        ? 'bg-amber-50 text-amber-700 border border-amber-300'
                        : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
                    title="Kutipan (Blockquote)"
                >
                    <Quote class="w-4 h-4" />
                </button>

                <!-- Code Block -->
                <button
                    type="button"
                    @click="editor?.chain().focus().toggleCodeBlock().run()"
                    class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors cursor-pointer"
                    :class="editor?.isActive('codeBlock')
                        ? 'bg-amber-50 text-amber-700 border border-amber-300'
                        : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
                    title="Blok Kode"
                >
                    <Code class="w-4 h-4" />
                </button>

                <!-- Bullet List -->
                <button
                    type="button"
                    @click="editor?.chain().focus().toggleBulletList().run()"
                    class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors cursor-pointer"
                    :class="editor?.isActive('bulletList')
                        ? 'bg-amber-50 text-amber-700 border border-amber-300'
                        : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
                    title="Daftar Poin (Bullet List)"
                >
                    <List class="w-4 h-4" />
                </button>

                <!-- Numbered List -->
                <button
                    type="button"
                    @click="editor?.chain().focus().toggleOrderedList().run()"
                    class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors cursor-pointer"
                    :class="editor?.isActive('orderedList')
                        ? 'bg-amber-50 text-amber-700 border border-amber-300'
                        : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
                    title="Daftar Nomor (Ordered List)"
                >
                    <ListOrdered class="w-4 h-4" />
                </button>

                <!-- Divider -->
                <div class="w-px h-5 bg-gray-200 mx-1" />

                <!-- Table Dropdown Button (Filament Style with Header Toggles) -->
                <div class="relative">
                    <button
                        type="button"
                        @click="showTableMenu = !showTableMenu"
                        class="h-8 px-2 rounded-lg flex items-center gap-1 transition-colors cursor-pointer text-xs font-semibold"
                        :class="editor?.isActive('table')
                            ? 'bg-amber-50 text-amber-700 border border-amber-300'
                            : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'"
                        title="Tabel & Pengaturan Header"
                    >
                        <TableIcon class="w-4 h-4" />
                        <ChevronDown class="w-3 h-3 opacity-70" />
                    </button>

                    <!-- Table Operations Popover -->
                    <div
                        v-if="showTableMenu"
                        class="absolute left-0 top-full mt-1.5 w-60 bg-white rounded-xl shadow-level-2 border border-gray-200 py-1.5 z-40 text-xs text-[#1A1A2E]"
                    >
                        <button
                            v-if="!editor?.isActive('table')"
                            type="button"
                            @click="insertTable"
                            class="w-full text-left px-3.5 py-2 hover:bg-amber-50 hover:text-amber-800 flex items-center gap-2 font-semibold"
                        >
                            <Plus class="w-4 h-4 text-amber-600" /> Buat Tabel Baru (3x3)
                        </button>
                        <template v-else>
                            <!-- Header Toggles Section -->
                            <div class="px-3.5 py-1 font-bold text-gray-400 uppercase text-[10px] tracking-wider">
                                Baris & Kolom Judul (Header)
                            </div>
                            <button
                                type="button"
                                @click="editor?.chain().focus().toggleHeaderRow().run(); showTableMenu = false"
                                class="w-full text-left px-3.5 py-1.5 hover:bg-amber-50 hover:text-amber-900 flex items-center justify-between font-medium"
                            >
                                <span class="flex items-center gap-2">
                                    <Rows class="w-3.5 h-3.5 text-amber-600" />
                                    Toggle Header Baris
                                </span>
                                <span class="text-[10px] text-gray-400">Row</span>
                            </button>
                            <button
                                type="button"
                                @click="editor?.chain().focus().toggleHeaderColumn().run(); showTableMenu = false"
                                class="w-full text-left px-3.5 py-1.5 hover:bg-amber-50 hover:text-amber-900 flex items-center justify-between font-medium"
                            >
                                <span class="flex items-center gap-2">
                                    <Columns class="w-3.5 h-3.5 text-amber-600" />
                                    Toggle Header Kolom
                                </span>
                                <span class="text-[10px] text-gray-400">Col</span>
                            </button>

                            <div class="my-1 border-t border-gray-100" />

                            <!-- Row / Col manipulations -->
                            <div class="px-3.5 py-1 font-bold text-gray-400 uppercase text-[10px] tracking-wider">
                                Struktur Baris & Kolom
                            </div>
                            <button
                                type="button"
                                @click="editor?.chain().focus().addRowAfter().run(); showTableMenu = false"
                                class="w-full text-left px-3.5 py-1.5 hover:bg-gray-100 flex items-center gap-2"
                            >
                                <span>+</span> Tambah Baris ke Bawah
                            </button>
                            <button
                                type="button"
                                @click="editor?.chain().focus().deleteRow().run(); showTableMenu = false"
                                class="w-full text-left px-3.5 py-1.5 hover:bg-red-50 text-red-600 flex items-center gap-2"
                            >
                                <span>-</span> Hapus Baris Ini
                            </button>
                            <button
                                type="button"
                                @click="editor?.chain().focus().addColumnAfter().run(); showTableMenu = false"
                                class="w-full text-left px-3.5 py-1.5 hover:bg-gray-100 flex items-center gap-2"
                            >
                                <span>+</span> Tambah Kolom ke Kanan
                            </button>
                            <button
                                type="button"
                                @click="editor?.chain().focus().deleteColumn().run(); showTableMenu = false"
                                class="w-full text-left px-3.5 py-1.5 hover:bg-red-50 text-red-600 flex items-center gap-2"
                            >
                                <span>-</span> Hapus Kolom Ini
                            </button>

                            <div class="my-1 border-t border-gray-100" />

                            <!-- Merge & Split -->
                            <button
                                type="button"
                                @click="editor?.chain().focus().mergeCells().run(); showTableMenu = false"
                                class="w-full text-left px-3.5 py-1.5 hover:bg-gray-100"
                            >
                                Gabung Sel Terpilih (Merge)
                            </button>
                            <button
                                type="button"
                                @click="editor?.chain().focus().splitCell().run(); showTableMenu = false"
                                class="w-full text-left px-3.5 py-1.5 hover:bg-gray-100"
                            >
                                Pisah Sel (Split)
                            </button>

                            <div class="my-1 border-t border-gray-100" />

                            <!-- Delete table -->
                            <button
                                type="button"
                                @click="editor?.chain().focus().deleteTable().run(); showTableMenu = false"
                                class="w-full text-left px-3.5 py-1.5 hover:bg-red-50 text-red-600 font-bold flex items-center gap-2"
                            >
                                <Trash2 class="w-3.5 h-3.5" /> Hapus Seluruh Tabel
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Image Upload -->
                <button
                    type="button"
                    @click="fileInputRef?.click()"
                    :disabled="isUploading"
                    class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition-colors cursor-pointer"
                    title="Sisipkan Foto / Gambar"
                >
                    <ImageIcon class="w-4 h-4" />
                </button>
            </div>

            <!-- Right: Undo & Redo -->
            <div class="flex items-center gap-1 shrink-0">
                <button
                    type="button"
                    @click="editor?.chain().focus().undo().run()"
                    :disabled="!editor?.can().undo()"
                    class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-600 hover:bg-gray-100 hover:text-gray-900 disabled:opacity-30 cursor-pointer"
                    title="Undo (Ctrl+Z)"
                >
                    <Undo class="w-4 h-4" />
                </button>
                <button
                    type="button"
                    @click="editor?.chain().focus().redo().run()"
                    :disabled="!editor?.can().redo()"
                    class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-600 hover:bg-gray-100 hover:text-gray-900 disabled:opacity-30 cursor-pointer"
                    title="Redo (Ctrl+Y)"
                >
                    <Redo class="w-4 h-4" />
                </button>
            </div>
        </div>

        <!-- Editor Content Area -->
        <div class="relative flex-1 bg-white">
            <div
                v-if="isUploading"
                class="absolute inset-0 bg-white/80 backdrop-blur-xs flex items-center justify-center z-20 text-xs font-bold text-[#003087]"
            >
                Mengunggah gambar...
            </div>
            <editor-content :editor="editor" />
        </div>

        <!-- Bottom helper bar -->
        <div class="px-4 py-1.5 bg-gray-50 border-t border-gray-200 text-[11px] text-gray-500 flex items-center justify-between">
            <span>Editor Tiptap Terintegrasi • Tabel, Gambar & Teks Kaya</span>
            <span>RT 09 & Karang Taruna</span>
        </div>
    </div>
</template>

<style>
/* Tiptap Editor & Placeholder */
.tiptap p.is-editor-empty:first-child::before {
    color: #94A3B8;
    content: attr(data-placeholder);
    float: left;
    height: 0;
    pointer-events: none;
}
</style>
