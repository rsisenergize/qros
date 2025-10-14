<div x-data="kioskApp()" class="min-h-screen">

    
    @livewire('kiosk::kiosk.welcome', ['restaurant' => $restaurant, 'shopBranch' => $shopBranch])

    @livewire('kiosk::kiosk.order-type', ['restaurant' => $restaurant, 'shopBranch' => $shopBranch])

    @livewire('kiosk::kiosk.menu', ['restaurant' => $restaurant, 'shopBranch' => $shopBranch])

    @livewire('kiosk::kiosk.item-customisation', ['restaurant' => $restaurant, 'shopBranch' => $shopBranch])

    @livewire('kiosk::kiosk.cart-summary', ['restaurant' => $restaurant, 'shopBranch' => $shopBranch])

    @livewire('kiosk::kiosk.payment-method', ['restaurant' => $restaurant, 'shopBranch' => $shopBranch])


    @push('scripts')
    <script>
        function kioskApp() {
            return {
                // 🟡 1. Welcome Screen
                currentScreen: 'welcome',
                
                // 🟡 2. Order Type Selection
                orderType: null,
                tableNumber: null,
                
                // 🟡 3. Menu Browsing
                searchQuery: '',
                showCart: false,
                selectedCategory: 'burgers',
                
                // 🟡 4. Item Selection & Customization
                cart: [],
                selectedItem: null,
                selectedVariant: null,
                itemQuantity: 1,
                
                // 🟡 5. Cart Summary
                customerInfo: {
                    name: '',
                    email: '',
                    phone: '',
                    pickupTime: '30'
                },
                
                // 🟡 6. Payment Method Selection
                paymentMethod: null,
                
                // 🟡 7. Order Confirmation
                orderNumber: null,


                // 🟡 1. Welcome Screen Methods
                startOrder() {
                    this.currentScreen = 'order-type';
                },

                // 🟡 2. Order Type Selection Methods
                selectOrderType(type) {
                    this.orderType = type;
                    // if (type === 'dine-in') {
                    //     this.currentScreen = 'table-entry';
                    // } else {
                    //     this.currentScreen = 'menu';
                    // }
                    this.currentScreen = 'menu';
                },

                // 🟡 Table Entry Methods
                scanQR() {
                    // Simulate QR scanning
                    this.tableNumber = Math.floor(Math.random() * 20) + 1;
                },

                confirmTable() {
                    if (this.tableNumber) {
                        this.currentScreen = 'menu';
                    }
                },

                // 🟡 3. Menu Browsing Methods
                get filteredItems() {
                    let items = this.menuItems.filter(item => item.category === this.selectedCategory);
                    if (this.searchQuery) {
                        items = items.filter(item => 
                            item.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                            item.description.toLowerCase().includes(this.searchQuery.toLowerCase())
                        );
                    }
                    return items;
                },

                // 🟡 4. Item Selection & Customization Methods
                selectItem() {
                    // this.selectedItem = { ...item };
                    // this.selectedVariant = item.variants ? item.variants[0].id : null;
                    // this.itemQuantity = 1;
                    this.currentScreen = 'item-customization';
                },

                selectVariant(variant) {
                    this.selectedVariant = variant.id;
                },

                increaseQuantity() {
                    this.itemQuantity++;
                },

                decreaseQuantity() {
                    if (this.itemQuantity > 1) {
                        this.itemQuantity--;
                    }
                },

                get totalItemPrice() {
                    if (!this.selectedItem) return 0;
                    
                    let totalPrice = this.selectedItem.price;
                    
                    // Add variant price
                    if (this.selectedItem.variants && this.selectedVariant) {
                        const variant = this.selectedItem.variants.find(v => v.id === this.selectedVariant);
                        if (variant) totalPrice += variant.price;
                    }

                    // Add addon prices
                    if (this.selectedItem.addons) {
                        this.selectedItem.addons.forEach(addon => {
                            if (addon.selected) {
                                totalPrice += addon.price;
                            }
                        });
                    }

                    return (totalPrice * this.itemQuantity).toFixed(2);
                },

                addToCartFromCustomization() {
                    if (!this.selectedItem) return;

                    // Calculate total price with variants and addons
                    let totalPrice = this.selectedItem.price;
                    
                    // Add variant price
                    if (this.selectedItem.variants && this.selectedVariant) {
                        const variant = this.selectedItem.variants.find(v => v.id === this.selectedVariant);
                        if (variant) totalPrice += variant.price;
                    }

                    // Add addon prices
                    let addonNames = [];
                    if (this.selectedItem.addons) {
                        this.selectedItem.addons.forEach(addon => {
                            if (addon.selected) {
                                totalPrice += addon.price;
                                addonNames.push(addon.name);
                            }
                        });
                    }

                    // Create cart item
                    const cartItem = {
                        id: this.selectedItem.id,
                        name: this.selectedItem.name,
                        price: totalPrice,
                        quantity: this.itemQuantity,
                        image: this.selectedItem.image,
                        variant: this.selectedItem.variants ? this.selectedItem.variants.find(v => v.id === this.selectedVariant)?.name : null,
                        addons: addonNames,
                        removals: this.selectedItem.removals ? this.selectedItem.removals.filter(r => r.selected).map(r => r.name) : []
                    };

                    // Check if item already exists in cart
                    const existingIndex = this.cart.findIndex(cartItem => cartItem.id === this.selectedItem.id);
                    if (existingIndex >= 0) {
                        this.cart[existingIndex].quantity += this.itemQuantity;
                    } else {
                        this.cart.push(cartItem);
                    }

                    // Reset and go back to menu
                    this.selectedItem = null;
                    this.selectedVariant = null;
                    this.itemQuantity = 1;
                    this.currentScreen = 'menu';
                    this.showCart = true;
                },

                removeFromCart(index) {
                    this.cart.splice(index, 1);
                },

                updateQuantity(index, change) {
                    const item = this.cart[index];
                    const newQuantity = item.quantity + change;
                    if (newQuantity > 0) {
                        item.quantity = newQuantity;
                    } else {
                        this.removeFromCart(index);
                    }
                },

                // 🟡 5. Cart Summary Methods
                get subtotal() {
                    return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0).toFixed(2);
                },

                get tax() {
                    return (this.subtotal * 0.08).toFixed(2);
                },

                get total() {
                    return (parseFloat(this.subtotal) + parseFloat(this.tax)).toFixed(2);
                },

                get isCustomerInfoValid() {
                    return this.customerInfo.name && 
                           this.customerInfo.email && 
                           this.customerInfo.phone;
                },

                saveCustomerInfo() {
                    this.currentScreen = 'payment';
                },

                // 🟡 6. Payment Method Selection Methods
                selectPaymentMethod(method) {
                    this.paymentMethod = method;
                },

                processPayment() {
                    // Livewire PaymentMethod component now creates the order and emits 'kiosk-order-confirmed'.
                },

                // 🟡 7. Order Confirmation Methods
                startNewOrder() {
                    // Reset all state
                    this.currentScreen = 'welcome';
                    this.orderType = null;
                    this.tableNumber = null;
                    this.searchQuery = '';
                    this.cart = [];
                    this.selectedItem = null;
                    this.selectedVariant = null;
                    this.itemQuantity = 1;
                    this.customerInfo = {
                        name: '',
                        email: '',
                        phone: '',
                        pickupTime: '30'
                    };
                    this.paymentMethod = null;
                    this.orderNumber = null;
                    
                    // Reset menu items
                    this.menuItems.forEach(item => {
                        if (item.addons) {
                            item.addons.forEach(addon => addon.selected = false);
                        }
                        if (item.removals) {
                            item.removals.forEach(removal => removal.selected = false);
                        }
                    });
                },

                proceedToCheckout() {
                    this.currentScreen = 'customer-info';
                }
            }
        }
    </script>
    @endpush

</div>