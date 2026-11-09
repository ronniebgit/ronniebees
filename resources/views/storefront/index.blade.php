@extends('layouts.storefront')

@section('content')
    <div class="rb-homepage">
        <section class="rb-topbar">
            <div class="rb-container rb-topbar-inner">
                <span>Ronnie Bee's NY Pizza</span>
                <span>Fresh slices • Pickup & delivery</span>
                <a href="tel:133012034202">+1 330 120 34202</a>
                <span>11 W. Main St, New York, NY</span>
            </div>
        </section>

        <header class="rb-header">
            <div class="rb-container rb-header-inner">
                <a href="{{ route('home') }}" class="rb-brand">
                    <div class="rb-brand-mark">R</div>
                    <div>
                        <strong>RONNIE</strong>
                        <small>Bee's</small>
                    </div>
                </a>

                <nav class="rb-nav">
                    <a href="{{ route('home') }}">Home</a>
                    <a href="{{ route('menu') }}">Menu</a>
                    <a href="{{ route('gift-card') }}">Gift Card</a>
                    <a href="{{ route('track') }}">Track Order</a>
                </nav>

                <a href="{{ route('cart') }}" class="rb-cart-button">
                    Cart <span>{{ collect(session('cart', []))->sum('quantity') }}</span>
                </a>
            </div>
        </header>

        <section class="rb-banner">
            <div class="rb-banner-slider">
                <article class="rb-banner-slide active slide-one">
                    <div class="rb-container rb-banner-shell">
                        <div class="rb-banner-copy">
                            <span class="rb-kicker">Fresh from the oven</span>
                            <h1>New York Style<br><span>Pizza</span></h1>
                            <h3>What is New York Style Pizza?</h3>
                            <p>New York style pizza is a very thin style of pizza characterized by its large, foldable slices and crispy outer crust. It is traditionally topped with tomato sauce and mozzarella cheese.</p>
                            <div class="rb-banner-actions">
                                <a href="{{ route('menu') }}" class="rb-btn rb-btn-primary">See Menu</a>
                            </div>
                        </div>

                        <div class="rb-banner-visual">
                            <div class="rb-photo-panel">
                                <img src="{{ asset('images/regular.jpg') }}" alt="New York style pizza" />
                            </div>
                            <div class="rb-mini-card">
                                <span>This week</span>
                                <h3>Pizza specials</h3>
                                <ul>
                                    <li><em>1-topping pie</em><strong>$18</strong></li>
                                    <li><em>Family combo</em><strong>$34</strong></li>
                                    <li><em>Loyalty points</em><strong>+1</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </article>

                <article class="rb-banner-slide slide-two">
                    <div class="rb-container rb-banner-shell">
                        <div class="rb-banner-copy">
                            <span class="rb-kicker">Best sellers</span>
                            <h1>Pepperoni<br><span>Favorite</span></h1>
                            <h3>Neighborhood favorite</h3>
                            <p>Classic slices, bold sauce, melted mozzarella, and a crisp finish that keeps customers coming back for more.</p>
                            <div class="rb-banner-actions">
                                <a href="{{ route('menu') }}" class="rb-btn rb-btn-primary">See Menu</a>
                            </div>
                        </div>

                        <div class="rb-banner-visual">
                            <div class="rb-photo-panel">
                                <img src="{{ asset('images/pepperoni.jpg') }}" alt="Pepperoni pizza" />
                            </div>
                            <div class="rb-mini-card">
                                <span>Popular picks</span>
                                <h3>Top sellers</h3>
                                <ul>
                                    <li><em>Margherita</em><strong>$21.99</strong></li>
                                    <li><em>Pepperoni</em><strong>$24.99</strong></li>
                                    <li><em>Buffalo Ranch</em><strong>$27.99</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </article>

                <article class="rb-banner-slide slide-three">
                    <div class="rb-container rb-banner-shell">
                        <div class="rb-banner-copy">
                            <span class="rb-kicker">Build your own</span>
                            <h1>BYO<br><span>Custom Pizza</span></h1>
                            <h3>Made your way</h3>
                            <p>Choose your size, sauce, split options, and toppings to create a pizza that feels like your own neighborhood favorite.</p>
                            <div class="rb-banner-actions">
                                <a href="{{ route('menu') }}" class="rb-btn rb-btn-primary">Build Yours</a>
                            </div>
                        </div>

                        <div class="rb-banner-visual">
                            <div class="rb-photo-panel">
                                <img src="{{ asset('images/cup.jpg') }}" alt="Pizza cup and specialty pie" />
                            </div>
                            <div class="rb-mini-card">
                                <span>Customize</span>
                                <h3>Choose your pie</h3>
                                <ul>
                                    <li><em>Whole pie</em><strong>Any style</strong></li>
                                    <li><em>Split half</em><strong>Half & half</strong></li>
                                    <li><em>Extras</em><strong>Fresh toppings</strong></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </article>
            </div>

            <div class="rb-slider-dots" aria-label="Slider pagination">
                <button class="rb-slider-dot active" type="button" data-slide="0" aria-label="Go to slide 1"></button>
                <button class="rb-slider-dot" type="button" data-slide="1" aria-label="Go to slide 2"></button>
                <button class="rb-slider-dot" type="button" data-slide="2" aria-label="Go to slide 3"></button>
            </div>
        </section>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const slides = Array.from(document.querySelectorAll('.rb-banner-slide'));
                const dots = Array.from(document.querySelectorAll('.rb-slider-dot'));
                let currentSlide = 0;
                let sliderTimer;

                function showSlide(index) {
                    currentSlide = (index + slides.length) % slides.length;
                    slides.forEach((slide, i) => slide.classList.toggle('active', i === currentSlide));
                    dots.forEach((dot, i) => dot.classList.toggle('active', i === currentSlide));
                }

                function startSlider() {
                    sliderTimer = setInterval(() => showSlide(currentSlide + 1), 5000);
                }

                dots.forEach((dot) => {
                    dot.addEventListener('click', () => {
                        showSlide(Number(dot.dataset.slide));
                        clearInterval(sliderTimer);
                        startSlider();
                    });
                });

                startSlider();
            });
        </script>

        <section class="rb-feature-strip">
            <div class="rb-container rb-feature-grid">
                <div class="rb-feature-box">
                    <div class="rb-feature-icon">🍕</div>
                    <h4>Fresh pizza</h4>
                    <p>New York style pies baked fresh and ready to order.</p>
                </div>
                <div class="rb-feature-box">
                    <div class="rb-feature-icon">🛒</div>
                    <h4>Easy ordering</h4>
                    <p>Customize your pizza and checkout in a few clicks.</p>
                </div>
                <div class="rb-feature-box">
                    <div class="rb-feature-icon">🏆</div>
                    <h4>Loyalty rewards</h4>
                    <p>Earn points with every order and redeem later.</p>
                </div>
            </div>
        </section>

        <section class="rb-menu-section">
            <div class="rb-container">
                <div class="rb-section-head">
                    <div>
                        <span>Shop by favorite</span>
                        <h2>Build your perfect pie</h2>
                    </div>
                    <a href="{{ route('menu') }}">View all menu</a>
                </div>

                <div class="rb-menu-quick-actions" style="display:flex; flex-wrap:wrap; gap:0.75rem; margin:1.25rem 0 2rem;">
                    <a href="{{ route('menu') }}#byo-pizza" class="rb-btn rb-btn-primary" style="display:inline-flex; align-items:center; justify-content:center; border-radius:999px; padding:0.75rem 1.25rem;">Build Your Own Pizza</a>
                    @foreach ($featuredProducts->take(4) as $product)
                        <a href="{{ route('product.show', $product) }}" class="rb-btn rb-btn-secondary" style="display:inline-flex; align-items:center; justify-content:center; border-radius:999px; padding:0.75rem 1.1rem; text-transform:none; letter-spacing:0.02em;">{{ $product->name }}</a>
                    @endforeach
                </div>

                <div class="rb-menu-grid">
                    @foreach ($featuredProducts as $product)
                        <article class="rb-card">
                            <div class="rb-card-visual">🍕</div>
                            <div class="rb-card-body">
                                <div class="rb-card-meta">
                                    <span class="rb-tag">Featured</span>
                                    <small>{{ $product->category->name ?? 'Pizza' }}</small>
                                </div>
                                <h3>{{ $product->name }}</h3>
                                <p>{{ Str::limit($product->description, 90) }}</p>
                                <div class="rb-card-footer">
                                    <div>
                                        <label>From</label>
                                        <strong>${{ number_format($product->medium_price, 2) }}</strong>
                                    </div>
                                    <a href="{{ route('product.show', $product) }}">Customize</a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="rb-category-section">
            <div class="rb-container rb-category-grid">
                @foreach ($categories as $category)
                    <article class="rb-category-card">
                        <span>{{ $category->products->count() }} items</span>
                        <h3>{{ $category->name }}</h3>
                        <p>{{ $category->description }}</p>
                    </article>
                @endforeach
            </div>
        </section>
    </div>
@endsection
