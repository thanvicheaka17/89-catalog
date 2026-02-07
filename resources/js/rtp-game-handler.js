
class RTPGameHandler {

    constructor(options = {}) {
        this.options = options;
        this.init();
    }

    init() {
        this.bindEvents();
    }

    bindEvents() {
        // Close modal when clicking outside
        const modal = document.getElementById('stepOverviewModal');
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    this.closeStepOverviewModal();
                }
            });
        }

        // Close modal on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const modal = document.getElementById('stepOverviewModal');
                if (modal && modal.style.display === 'flex') {
                    this.closeStepOverviewModal();
                }
            }
        });
    }

    showStepOverviewModal(gameData) {
        const modal = document.getElementById('stepOverviewModal');
        if (!modal) return;
        
        // Set game basic info
        const gameImage = document.getElementById('stepModalGameImage');
        const gameName = document.getElementById('stepModalGameName');
        const provider = document.getElementById('stepModalProvider');
        const rtp = document.getElementById('stepModalRTP');
        const pola = document.getElementById('stepModalPola');
        const rating = document.getElementById('stepModalRating');
        const stakeBet = document.getElementById('stepModalStakeBet');

        if (gameImage) gameImage.src = gameData.img_src || '';
        if (gameImage) gameImage.alt = gameData.name || '';
        if (gameName) gameName.textContent = gameData.name || 'N/A';
        if (provider) provider.textContent = gameData.provider || 'N/A';
        if (rtp) rtp.textContent = "RTP: " + (gameData.rtp || 0) + '%';
        if (pola) pola.textContent = "POLA: " + (gameData.pola || 'N/A') + '%';
        if (rating) rating.textContent = "Rating: " + (gameData.rating || 'N/A');
        if (stakeBet) stakeBet.textContent = gameData.stake_bet ? this.numberFormat(gameData.stake_bet) : 'N/A';

        // Helper function to show/hide step
        const setStep = (stepNum, step, type, desc) => {
            const container = document.getElementById(`step${stepNum}Container`);
            const valueEl = document.getElementById(`step${stepNum}Value`);
            const typeEl = document.getElementById(`step${stepNum}Type`);
            const descEl = document.getElementById(`step${stepNum}Desc`);

            if (container && valueEl && typeEl && descEl) {
                if (step && step > 0) {
                    container.style.display = 'block';
                    valueEl.textContent = this.numberFormat(step);
                    typeEl.textContent = type || 'N/A';
                    descEl.textContent = desc || 'No description available';
                } else {
                    container.style.display = 'none';
                }
            }
        };

        // Set all steps
        setStep('One', gameData.step_one, gameData.type_step_one, gameData.desc_step_one);
        setStep('Two', gameData.step_two, gameData.type_step_two, gameData.desc_step_two);
        setStep('Three', gameData.step_three, gameData.type_step_three, gameData.desc_step_three);
        setStep('Four', gameData.step_four, gameData.type_step_four, gameData.desc_step_four);

        // Show modal
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    closeStepOverviewModal() {
        const modal = document.getElementById('stepOverviewModal');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    numberFormat(number) {
        if (number === null || number === undefined) return 'N/A';
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
}

// Make functions globally available for inline onclick handlers
window.showStepOverviewModal = function(gameData) {
    if (window.rtpGameHandler) {
        window.rtpGameHandler.showStepOverviewModal(gameData);
    }
};

window.closeStepOverviewModal = function() {
    if (window.rtpGameHandler) {
        window.rtpGameHandler.closeStepOverviewModal();
    }
};

window.number_format = function(number) {
    if (window.rtpGameHandler) {
        return window.rtpGameHandler.numberFormat(number);
    }
    if (number === null || number === undefined) return 'N/A';
    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
};

export default RTPGameHandler;