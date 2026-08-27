@extends('hub.layout')

@section('content')
<div class="container mx-auto px-4 py-8" x-data="botDashboard()">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Panel de Control de Bots</h1>
    </div>

    <!-- Pestañas de Aplicación -->
    <div class="flex space-x-2 border-b border-gray-200 mb-6">
        <template x-for="app in apps" :key="app.id">
            <button 
                @click="selectApp(app.id)"
                :class="{'border-b-2 border-blue-500 text-blue-600': currentApp === app.id, 'text-gray-500 hover:text-gray-700': currentApp !== app.id}"
                class="py-2 px-4 text-sm font-medium focus:outline-none transition-colors"
                x-text="app.name">
            </button>
        </template>
    </div>

    <div class="flex flex-col md:flex-row gap-6 h-[700px]">
        
        <!-- Lista de Chats -->
        <div class="w-full md:w-1/3 bg-white rounded-lg shadow-sm border border-gray-200 flex flex-col">
            <div class="p-4 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                <h2 class="text-lg font-semibold text-gray-700">Chats Activos</h2>
            </div>
            <div class="flex-1 overflow-y-auto p-2">
                <template x-if="chats.length === 0">
                    <div class="text-center text-gray-500 p-4">No hay chats para esta aplicación.</div>
                </template>
                <template x-for="chat in chats" :key="chat.phone_number">
                    <div 
                        @click="selectChat(chat.phone_number)"
                        :class="{'bg-blue-50 border-blue-200': currentChat === chat.phone_number, 'hover:bg-gray-50 border-transparent': currentChat !== chat.phone_number}"
                        class="p-3 mb-2 rounded-lg cursor-pointer border transition-all flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-600 font-bold">
                            <span x-text="chat.contact_name ? chat.contact_name.charAt(0).toUpperCase() : '?'"></span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800" x-text="chat.contact_name || 'Desconocido'"></p>
                            <p class="text-xs text-gray-500" x-text="chat.phone_number"></p>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Área de Mensajes -->
        <div class="w-full md:w-2/3 bg-white rounded-lg shadow-sm border border-gray-200 flex flex-col relative">
            <template x-if="!currentChat">
                <div class="absolute inset-0 flex items-center justify-center bg-gray-50 rounded-lg">
                    <p class="text-gray-500">Selecciona un chat para ver los mensajes.</p>
                </div>
            </template>

            <template x-if="currentChat">
                <div class="flex flex-col h-full">
                    <!-- Header Chat -->
                    <div class="p-4 border-b border-gray-200 bg-gray-50 rounded-t-lg">
                        <h2 class="text-lg font-semibold text-gray-700" x-text="'Chat con ' + currentChat"></h2>
                    </div>

                    <!-- Mensajes -->
                    <div class="flex-1 overflow-y-auto p-4 space-y-4" id="messages-container">
                        <template x-if="messages.length === 0">
                            <div class="text-center text-gray-500 p-4">No hay mensajes.</div>
                        </template>
                        <template x-for="msg in messages" :key="msg.id">
                            <div :class="{'flex justify-end': msg.is_from_bot, 'flex justify-start': !msg.is_from_bot}">
                                <div 
                                    :class="{'bg-blue-600 text-white': msg.is_from_bot, 'bg-gray-100 text-gray-800': !msg.is_from_bot}"
                                    class="max-w-[70%] rounded-2xl px-4 py-2 shadow-sm">
                                    <p class="text-sm" x-text="msg.body"></p>
                                    <p :class="{'text-blue-200': msg.is_from_bot, 'text-gray-400': !msg.is_from_bot}" class="text-xs mt-1 text-right" x-text="new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></p>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Input Mensaje -->
                    <div class="p-4 border-t border-gray-200 bg-white rounded-b-lg">
                        <form @submit.prevent="sendMessage" class="flex gap-2">
                            <input 
                                type="text" 
                                x-model="newMessage" 
                                placeholder="Escribe un mensaje..." 
                                class="flex-1 border border-gray-300 rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
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
    // Usamos AlpineJS que probablemente ya está en el hub, si no lo cargamos
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

            init() {
                this.loadChats();
                
                // Refrescar mensajes cada 5 segundos
                this.pollInterval = setInterval(() => {
                    if (this.currentChat) {
                        this.loadMessages(this.currentChat, true);
                    }
                }, 5000);
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
                } catch (error) {
                    console.error('Error cargando chats:', error);
                }
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
                } catch (error) {
                    console.error('Error cargando mensajes:', error);
                }
            },

            async sendMessage() {
                if (!this.newMessage.trim() || !this.currentChat) return;

                this.sending = true;
                const body = this.newMessage;
                this.newMessage = '';

                // Añadir a UI optimísticamente
                this.messages.push({
                    id: Date.now(),
                    body: body,
                    is_from_bot: true,
                    created_at: new Date().toISOString()
                });
                this.scrollToBottom();

                try {
                    await fetch('/api/bot/web-send', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            app: this.currentApp,
                            phone_number: this.currentChat,
                            message: body
                        })
                    });
                    
                    // Recargar real tras confirmar
                    await this.loadMessages(this.currentChat);
                } catch (error) {
                    console.error('Error enviando mensaje:', error);
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

