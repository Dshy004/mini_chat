<script>
        const app = Vue.createApp({
            data() {
                return {
                    btnU: false,
                    divU: true,
                    divDocs: false,
                    docsName: "",
                    isInvalidFile: false,
                    emojiList: false,
                };
            },
            methods: {
                handleResize() {
                    this.btnU = window.innerWidth <= 1000;
                    this.divU = window.innerWidth > 1000;
                },
                showListU() {
                    this.divU = !this.divU;
                },
                onFileChange(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const allowedTypes = [
                            'image/png', 'image/jpg', 'image/jpeg', 'image/webp',
                            'video/mp4', 'video/webm', 'audio/mpeg', 
                            'application/pdf', 'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                            'text/plain', 'text/csv', 'application/zip',
                        ];            
                        this.divDocs = true;

                        if (allowedTypes.includes(file.type)) {
                            this.docsName = file.name;
                            this.isInvalidFile = false;
                        } else {
                            this.docsName = "Ce format de fichier n'est pas accepté";
                            this.isInvalidFile = true;
                            document.getElementById("docs").value = "";
                        }
                    }
                },
                HideDocs() {
                    this.divDocs = false;
                    this.isInvalidFile = false;
                    document.getElementById("docs").value = "";
                },
                showEmojiList() {
                    this.emojiList = !this.emojiList;
                }
            },
            mounted() {
                window.addEventListener('resize', this.handleResize);
                this.handleResize();
            },
            beforeUnmount() {
                window.removeEventListener('resize', this.handleResize);
            },
        });

        app.mount('#app');
    </script>
