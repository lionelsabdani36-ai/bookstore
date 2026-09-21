# Frontend Implementation Plan

You are a senior full-stack developer acting as the Frontend Developer for this project.

The workspace is `/home/whitejack/Projects/bookstore`.
Your task is to scaffold and implement the Nuxt 3 frontend in a new directory `/home/whitejack/Projects/bookstore/frontend`.
Since it doesn't exist yet, you must first create it:
`npx nuxi@latest init frontend --packageManager npm --gitInit false` (accept prompts or run non-interactively).

1. **Setup & Config**
   - Install Tailwind CSS: `npm install -D @nuxtjs/tailwindcss`
   - Install Pinia: `npm install @pinia/nuxt pinia`
   - Configure `nuxt.config.ts` to include these modules.
   - Configure Axios or use built-in `$fetch`/useFetch for API calls (base URL: `http://localhost:8000/api/v1`).
   
2. **Pages & Routing**
   - Public pages: `/` (Landing), `/login`, `/register`, `/forgot-password` (2-step), `/our-story`, `/blog`, `/contact`.
   - Admin pages (`/admin/...`): Dashboard, Categories, Books, Users, Orders (Cashier confirm), Reports, Chat.
   - User pages (`/user/...`): Dashboard (Transaksi/Catalog), Keranjang (Cart), Riwayat Pesanan (History), Chat.

3. **Components**
   - `Sidebar.vue`: For both Admin and User (different links based on role).
   - `DataTable.vue`: Reusable table component.
   - `BookCard.vue`: Card for displaying a book in the catalog.
   - `ChatWindow.vue`: Two-pane chat UI.
   - `InvoiceView.vue`: Printable layout.

4. **Stores (Pinia)**
   - `auth`: Handle user state, token (stored in cookie/local storage).
   - `cart`: Handle cart state (Add to cart, total price, checkout).

5. **Styling**
   - Follow the spec: two-column layout for auth, left sidebar for dashboard. Rounded cards, soft gradient sidebar, blue/purple accents.

**IMPORTANT RULES:**
- Wait for backend endpoints to be ready or mock them temporarily. The backend developer is working on them in parallel.
- Ensure clean code and proper Vue 3 Composition API usage.
- Reply when you are done with the frontend implementation.
