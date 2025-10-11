class IconPicker {
    constructor(selector, options = {}) {
        this.container = document.querySelector(selector);
        this.options = {
            icons: options.icons || [],
            showSelectedIn: options.showSelectedIn || null,
            defaultValue: options.defaultValue || '',
            searchable: options.searchable !== false,
            containerClass: options.containerClass || '',
            selectedClass: options.selectedClass || 'selected',
            hideOnSelect: options.hideOnSelect !== false,
            fade: options.fade !== false,
            valueFormat: options.valueFormat || (val => val)
        };

        this.selectedIcon = this.options.defaultValue;
        this.init();
    }

    init() {
        this.createPicker();
        this.bindEvents();
        if (this.options.showSelectedIn) {
            this.updateSelectedIcon();
        }
    }

    createPicker() {
        const pickerContainer = document.createElement('div');
        pickerContainer.className = `icon-picker-container ${this.options.containerClass}`;

        if (this.options.searchable) {
            const searchInput = document.createElement('input');
            searchInput.type = 'text';
            searchInput.className = 'icon-picker-search';
            searchInput.placeholder = 'Search icons...';
            pickerContainer.appendChild(searchInput);
        }

        const iconsContainer = document.createElement('div');
        iconsContainer.className = 'icon-picker-icons';
        this.options.icons.forEach(icon => {
            const iconElement = document.createElement('div');
            iconElement.className = 'icon-picker-icon';
            iconElement.innerHTML = `<i class="${icon}"></i>`;
            iconElement.dataset.icon = icon;
            iconsContainer.appendChild(iconElement);
        });

        pickerContainer.appendChild(iconsContainer);
        this.container.appendChild(pickerContainer);
    }

    bindEvents() {
        const icons = this.container.querySelectorAll('.icon-picker-icon');
        icons.forEach(icon => {
            icon.addEventListener('click', () => this.selectIcon(icon));
        });

        if (this.options.searchable) {
            const searchInput = this.container.querySelector('.icon-picker-search');
            searchInput.addEventListener('input', (e) => this.filterIcons(e.target.value));
        }
    }

    selectIcon(iconElement) {
        this.selectedIcon = iconElement.dataset.icon;
        this.updateSelectedIcon();

        if (this.options.hideOnSelect) {
            this.container.querySelector('.icon-picker-container').style.display = 'none';
        }
    }

    updateSelectedIcon() {
        if (this.options.showSelectedIn) {
            const formattedIcon = this.options.valueFormat(this.selectedIcon);
            this.options.showSelectedIn.innerHTML = `<i class="${formattedIcon}"></i>`;
        }
    }

    filterIcons(searchTerm) {
        const icons = this.container.querySelectorAll('.icon-picker-icon');
        icons.forEach(icon => {
            const iconClass = icon.dataset.icon.toLowerCase();
            if (iconClass.includes(searchTerm.toLowerCase())) {
                icon.style.display = '';
            } else {
                icon.style.display = 'none';
            }
        });
    }

    set(icon = '') {
        this.selectedIcon = icon;
        this.updateSelectedIcon();
    }

    get() {
        return this.selectedIcon;
    }
}
