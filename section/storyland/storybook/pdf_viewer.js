/**
 * Client-Side PDF Viewer using PDF.js
 * No server requirements - works entirely in the browser
 */

class PDFViewer {
    constructor(containerId, options = {}) {
        this.container = document.getElementById(containerId);
        this.options = {
            scale: options.scale || 1.5,
            pageNumber: options.pageNumber || 1,
            ...options
        };
        this.pdfDoc = null;
        this.currentPage = 1;
        this.totalPages = 0;
        this.isLoading = false;
        
        // Load PDF.js library dynamically
        this.loadPDFJS();
    }
    
    async loadPDFJS() {
        if (window.pdfjsLib) {
            this.initializeViewer();
            return;
        }
        
        // Load PDF.js from CDN
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js';
        script.onload = () => {
            // Configure PDF.js
            window.pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
            this.initializeViewer();
        };
        document.head.appendChild(script);
    }
    
    async initializeViewer() {
        // Create canvas element for PDF rendering
        this.canvas = document.createElement('canvas');
        this.canvas.style.maxWidth = '100%';
        this.canvas.style.height = 'auto';
        this.canvas.style.border = '1px solid #ddd';
        this.canvas.style.borderRadius = '8px';
        this.canvas.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
        
        this.container.appendChild(this.canvas);
        
        // Add loading indicator
        this.loadingDiv = document.createElement('div');
        this.loadingDiv.innerHTML = '<div style="text-align: center; padding: 20px;"><i class="fas fa-spinner fa-spin"></i> Loading PDF...</div>';
        this.loadingDiv.style.display = 'none';
        this.container.appendChild(this.loadingDiv);
    }
    
    async loadPDF(pdfUrl) {
        if (this.isLoading) return;
        
        this.isLoading = true;
        this.showLoading(true);
        
        try {
            // Load PDF document
            this.pdfDoc = await window.pdfjsLib.getDocument(pdfUrl).promise;
            this.totalPages = this.pdfDoc.numPages;
            this.currentPage = 1;
            
            // Render first page
            await this.renderPage(1);
            
            this.showLoading(false);
            this.isLoading = false;
            
            // Trigger custom event
            this.container.dispatchEvent(new CustomEvent('pdfLoaded', {
                detail: { totalPages: this.totalPages, currentPage: this.currentPage }
            }));
            
        } catch (error) {
            console.error('Error loading PDF:', error);
            this.showError('Failed to load PDF: ' + error.message);
            this.isLoading = false;
        }
    }
    
    async renderPage(pageNumber) {
        if (!this.pdfDoc || pageNumber < 1 || pageNumber > this.totalPages) {
            return;
        }
        
        this.currentPage = pageNumber;
        
        try {
            const page = await this.pdfDoc.getPage(pageNumber);
            const viewport = page.getViewport({ scale: this.options.scale });
            
            // Set canvas dimensions
            this.canvas.width = viewport.width;
            this.canvas.height = viewport.height;
            
            // Render PDF page to canvas
            const renderContext = {
                canvasContext: this.canvas.getContext('2d'),
                viewport: viewport
            };
            
            await page.render(renderContext).promise;
            
            // Trigger custom event
            this.container.dispatchEvent(new CustomEvent('pageRendered', {
                detail: { pageNumber: this.currentPage, totalPages: this.totalPages }
            }));
            
        } catch (error) {
            console.error('Error rendering page:', error);
            this.showError('Failed to render page: ' + error.message);
        }
    }
    
    async nextPage() {
        if (this.currentPage < this.totalPages) {
            await this.renderPage(this.currentPage + 1);
        }
    }
    
    async prevPage() {
        if (this.currentPage > 1) {
            await this.renderPage(this.currentPage - 1);
        }
    }
    
    async goToPage(pageNumber) {
        if (pageNumber >= 1 && pageNumber <= this.totalPages) {
            await this.renderPage(pageNumber);
        }
    }
    
    showLoading(show) {
        if (this.loadingDiv) {
            this.loadingDiv.style.display = show ? 'block' : 'none';
        }
    }
    
    showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.innerHTML = `<div style="text-align: center; padding: 20px; color: #dc3545; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 8px; margin: 10px 0;">${message}</div>`;
        this.container.appendChild(errorDiv);
    }
    
    // Get current page info
    getCurrentPage() {
        return this.currentPage;
    }
    
    getTotalPages() {
        return this.totalPages;
    }
    
    // Check if PDF is loaded
    isLoaded() {
        return this.pdfDoc !== null;
    }
    
    // Update scale
    setScale(scale) {
        this.options.scale = scale;
        if (this.isLoaded()) {
            this.renderPage(this.currentPage);
        }
    }
}

// Export for use in other scripts
window.PDFViewer = PDFViewer;
