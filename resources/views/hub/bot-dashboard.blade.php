@extends('hub.layout')

@section('content')
<div class="container mx-auto px-4 py-8" x-data="botDashboard()">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-200">Panel de Control de Bots</h1>
        
        <!-- Toggle Acción Humana -->
        <div class="flex items-center space-x-3 bg-slate-800 px-4 py-2 rounded-lg border border-slate-700">
            <span class="text-sm font-medium" :class="humanAction ? 'text-blue-400' : 'text-gray-400'">
                Acción Humana
            </span>
            <button 
                @click="toggleSettings()"
                type="button" 
                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                :class="humanAction ? 'bg-blue-600' : 'bg-gray-600'">
                <span 
                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                    :class="humanAction ? 'translate-x-5' : 'translate-x-0'">
                </span>
            </button>
            <span class="text-xs" :class="humanAction ? 'text-green-400' : 'text-red-400'" x-text="humanAction ? 'Requerida' : 'Auto'"></span>
        </div>
    </div>

    <!-- Pestañas de Aplicación -->
    <div class="flex space-x-2 border-b border-slate-700 mb-6">
        <template x-for="app in apps" :key="app.id">
            <button 
                @click="selectApp(app.id)"
                :class="{'border-b-2 border-blue-500 text-blue-400': currentApp === app.id, 'text-gray-400 hover:text-gray-200': currentApp !== app.id}"
                class="py-2 px-4 text-sm font-medium focus:outline-none transition-colors"
                x-text="app.name">
            </button>
        </template>
    </div>

    <div class="flex flex-col md:flex-row gap-6 h-[700px]">
        
        <!-- Lista de Chats -->
        <div class="w-full md:w-1/3 bg-slate-900 rounded-lg shadow-sm border border-slate-700 flex flex-col">
            <div class="p-4 border-b border-slate-700 bg-slate-800 rounded-t-lg">
                <h2 class="text-lg font-semibold text-gray-200">Chats Activos</h2>
            </div>
            <div class="flex-1 overflow-y-auto p-2">
                <template x-if="chats.length === 0">
                    <div class="text-center text-gray-500 p-4">No hay chats para esta aplicación.</div>
                </template>
                <template x-for="chat in chats" :key="chat.phone_number">
                    <div 
                        @click="selectChat(chat.phone_number)"
                        :class="{'bg-slate-800 border-slate-600': currentChat === chat.phone_number, 'hover:bg-slate-800 border-transparent': currentChat !== chat.phone_number}"
                        class="p-3 mb-2 rounded-lg cursor-pointer border transition-all flex items-center gap-3">
                        <div class="w-10 h-10 bg-slate-700 rounded-full flex items-center justify-center text-gray-300 font-bold">
                            <span x-text="chat.contact_name ? chat.contact_name.charAt(0).toUpperCase() : '?'"></span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-200" x-text="chat.contact_name || 'Desconocido'"></p>
                            <p class="text-xs text-gray-400" x-text="chat.phone_number"></p>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Área de Mensajes -->
        <div class="w-full md:w-2/3 bg-slate-900 rounded-lg shadow-sm border border-slate-700 flex flex-col relative">
            <template x-if="!currentChat">
                <div class="absolute inset-0 flex items-center justify-center bg-slate-900 rounded-lg">
                    <p class="text-gray-500">Selecciona un chat para ver los mensajes.</p>
                </div>
            </template>

            <template x-if="currentChat">
                <div class="flex flex-col h-full">
                    <!-- Header Chat -->
                    <div class="p-4 border-b border-slate-700 bg-slate-800 rounded-t-lg">
                        <h2 class="text-lg font-semibold text-gray-200" x-text="'Chat con ' + currentChat"></h2>
                    </div>

                    <!-- Mensajes -->
                    <div class="flex-1 overflow-y-auto p-4 space-y-4" id="messages-container">
                        <template x-if="messages.length === 0">
                            <div class="text-center text-gray-500 p-4">No hay mensajes.</div>
                        </template>
                        <template x-for="msg in messages" :key="msg.id">
                            <div :class="{'flex justify-end': msg.is_from_bot, 'flex justify-start': !msg.is_from_bot}" class="group relative">
                                <div class="flex flex-col" :class="{'items-end': msg.is_from_bot, 'items-start': !msg.is_from_bot}">
                                    
                                    <div class="flex items-center gap-2" :class="{'flex-row-reverse': !msg.is_from_bot}">
                                        <div 
                                            :class="{
                                                'bg-blue-600 text-white': msg.is_from_bot && msg.status !== 'draft', 
                                                'bg-yellow-600 text-white border-2 border-yellow-500': msg.is_from_bot && msg.status === 'draft', 
                                                'bg-slate-700 text-gray-200': !msg.is_from_bot
                                            }"
                                            class="max-w-xl rounded-2xl px-4 py-2 shadow-sm relative">
                                            <p class="text-sm" x-text="msg.body"></p>
                                            <div class="flex items-center gap-2 mt-1 justify-end">
                                                <span x-show="msg.status === 'draft'" class="text-[10px] bg-yellow-800 px-1 rounded font-bold uppercase">Borrador IA</span>
                                                <p :class="{'text-blue-200': msg.is_from_bot, 'text-gray-400': !msg.is_from_bot}" class="text-xs text-right" x-text="new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></p>
                                            </div>
                                        </div>

                                        <!-- Botón Eliminar -->
                                        <button @click="deleteMessage(msg.id)" class="opacity-0 group-hover:opacity-100 text-red-400 hover:text-red-300 transition-opacity p-1 bg-slate-800 rounded-full" title="Eliminar mensaje">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                    
                                    <!-- Botón Aprobar si es borrador -->
                                    <template x-if="msg.status === 'draft'">
                                        <button @click="approveDraft(msg.id)" class="mt-1 text-xs bg-green-600 hover:bg-green-500 text-white px-3 py-1 rounded-full flex items-center gap-1 transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Aprobar y Enviar
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Input Mensaje -->
                    <div class="p-4 border-t border-slate-700 bg-slate-800 rounded-b-lg">
                        <form @submit.prevent="sendMessage" class="flex gap-2">
                            <input 
                                type="text" 
                                x-model="newMessage" 
                                placeholder="Escribe un mensaje..." 
                                class="flex-1 bg-slate-700 text-gray-100 border border-slate-600 rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                :disabled="sending">
                            <button 
                                type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white rounded-full px-6 py-2 font-medium transition-colors disabled:opacity-50"
                                :disabled="sending || !newMessage.trim()">
                                <span x-show="!sending">Enviar</span>
                                <span x-show="sending">...</span>
                            </button>
                        </form>
                    </div>
                </div>
            </template>
        </div>

    </div>
</div>

<script>
    if (typeof Alpine === 'undefined') {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js';
        script.defer = true;
        document.head.appendChild(script);
    }

    document.addEventListener('alpine:init', () => {
        Alpine.data('botDashboard', () => ({
            apps: [
                { id: 'whatsapp', name: 'General' },
                { id: 'love-widget', name: 'Love Widget' },
                { id: 'enfoca', name: 'Enfoca' }
            ],
            currentApp: 'whatsapp',
            chats: [],
            messages: [],
            currentChat: null,
            newMessage: '',
            sending: false,
            pollInterval: null,
            humanAction: false,

            init() {
                this.loadSettings();
                this.loadChats();
                
                this.pollInterval = setInterval(() => {
                    if (this.currentChat) {
                        this.loadMessages(this.currentChat, true);
                    }
                    // También actualizar los chats
                    if(document.visibilityState === 'visible') {
                        this.loadChats();
                    }
                }, 5000);
            },

            async loadSettings() {
                try {
                    const res = await fetch('/api/bot/settings');
                    const data = await res.json();
                    this.humanAction = data.human_action;
                } catch (e) {}
            },

            async toggleSettings() {
                try {
                    const res = await fetch('/api/bot/settings/toggle', { method: 'POST' });
                    const data = await res.json();
                    this.humanAction = data.human_action;
                } catch (e) {}
            },

            selectApp(appId) {
                this.currentApp = appId;
                this.currentChat = null;
                this.messages = [];
                this.loadChats();
            },

            async loadChats() {
                try {
                    const res = await fetch(`/api/bot/chats?app=${this.currentApp}`);
                    this.chats = await res.json();
                } catch (error) {}
            },

            selectChat(phone) {
                this.currentChat = phone;
                this.loadMessages(phone);
            },

            async loadMessages(phone, isPolling = false) {
                try {
                    const res = await fetch(`/api/bot/messages/${phone}?app=${this.currentApp}`);
                    const data = await res.json();
                    
                    const isNewMessage = this.messages.length !== data.length;
                    this.messages = data;

                    if (isNewMessage && !isPolling) {
                        this.scrollToBottom();
                    }
                } catch (error) {}
            },

            async approveDraft(id) {
                try {
                    await fetch('/api/bot/approve-draft', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: id })
                    });
                    await this.loadMessages(this.currentChat);
                } catch (error) {
                    alert("Error al aprobar.");
                }
            },

            async deleteMessage(id) {
                if (!confirm('¿Seguro que quieres eliminar este mensaje?')) return;
                
                try {
                    await fetch(`/api/bot/messages/${id}`, {
                        method: 'DELETE',
                        headers: { 'Content-Type': 'application/json' }
                    });
                    
                    // Recargar mensajes
                    if (this.currentChat) {
                        await this.loadMessages(this.currentChat);
                    }
                } catch (error) {
                    alert("Error al eliminar.");
                }
            },

            async sendMessage() {
                if (!this.newMessage.trim() || !this.currentChat) return;

                this.sending = true;
                const body = this.newMessage;
                this.newMessage = '';

                try {
                    await fetch('/api/bot/web-send', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                        body: JSON.stringify({
                            app: this.currentApp,
                            phone_number: this.currentChat,
                            message: body
                        })
                    });
                    
                    await this.loadMessages(this.currentChat);
                    this.scrollToBottom();
                } catch (error) {
                    alert("Hubo un error al enviar el mensaje.");
                } finally {
                    this.sending = false;
                }
            },

            scrollToBottom() {
                setTimeout(() => {
                    const container = document.getElementById('messages-container');
                    if (container) {
                        container.scrollTop = container.scrollHeight;
                    }
                }, 100);
            }
        }));
    });
</script>
@endsection
