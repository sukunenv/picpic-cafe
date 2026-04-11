import { useState } from "react";
import { ArrowLeft, Trash2, MapPin, Wallet, CreditCard, Tag, ChevronRight } from "lucide-react";
import { Link, useNavigate } from "react-router";
import { motion } from "motion/react";

const initialCartItems = [
  {
    id: "1",
    name: "Cappuccino Premium",
    price: 35000,
    quantity: 2,
    image: "https://images.unsplash.com/photo-1563390323222-b0593bfc85c3?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxjYXBwdWNjaW5vJTIwY29mZmVlJTIwYXJ0fGVufDF8fHx8MTc3NDgxMjc4OXww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral",
  },
  {
    id: "2",
    name: "Classic Burger",
    price: 48000,
    quantity: 1,
    image: "https://images.unsplash.com/photo-1625331725309-83e4f3c1373b?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxidXJnZXIlMjBkZWxpY2lvdXN8ZW58MXx8fHwxNzc0ODYxMzMwfDA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral",
  },
];

const paymentMethods = [
  { id: "ewallet", name: "E-Wallet", icon: Wallet, detail: "GoPay, OVO, Dana" },
  { id: "card", name: "Credit Card", icon: CreditCard, detail: "Visa, Mastercard" },
];

export function CartScreen() {
  const navigate = useNavigate();
  const [cartItems, setCartItems] = useState(initialCartItems);
  const [selectedPayment, setSelectedPayment] = useState("ewallet");
  const [name, setName] = useState("");
  const [address, setAddress] = useState("");
  const [phone, setPhone] = useState("");

  const subtotal = cartItems.reduce((sum, item) => sum + item.price * item.quantity, 0);
  const deliveryFee = 10000;
  const discount = 5000;
  const total = subtotal + deliveryFee - discount;

  const removeItem = (id: string) => {
    setCartItems(cartItems.filter((item) => item.id !== id));
  };

  const handleCheckout = () => {
    // Checkout logic here
    alert("Pesanan berhasil! Terima kasih sudah memesan di PICPIC.");
  };

  return (
    <div className="min-h-screen pb-32 bg-[#F8F7FF]">
      {/* Clean Header */}
      <div className="bg-white sticky top-0 z-20 border-b border-[#2D2B55]/5">
        <div className="px-6 pt-12 pb-4">
          <div className="flex items-center gap-4">
            <Link to="/" className="p-2 hover:bg-[#F8F7FF] rounded-full transition-colors">
              <ArrowLeft className="text-[#2D2B55]" size={24} />
            </Link>
            <div className="flex-1">
              <h1 className="text-[#2D2B55] font-bold text-2xl">Keranjang</h1>
              <p className="text-[#2D2B55]/60 text-sm">{cartItems.length} item</p>
            </div>
          </div>
        </div>
      </div>

      <motion.div
        initial={{ opacity: 0 }}
        animate={{ opacity: 1 }}
        className="px-6 mt-6"
      >
        {/* Cart Items */}
        {cartItems.length === 0 ? (
          <div className="text-center py-20">
            <div className="w-20 h-20 bg-[#C9BEFF]/20 rounded-full mx-auto mb-4 flex items-center justify-center">
              <Tag className="text-[#6367FF]" size={32} />
            </div>
            <p className="text-[#2D2B55]/60 text-base mb-6">Keranjang masih kosong</p>
            <Link
              to="/menu"
              className="inline-block px-8 py-3 bg-[#6367FF] text-white rounded-full font-bold active:scale-95 transition-transform"
            >
              Belanja Sekarang
            </Link>
          </div>
        ) : (
          <>
            {/* Items List */}
            <div className="mb-8">
              <h2 className="text-[#2D2B55] font-bold text-lg mb-4">Pesanan Anda</h2>
              <div className="space-y-3">
                {cartItems.map((item, index) => (
                  <motion.div
                    key={item.id}
                    initial={{ x: -20, opacity: 0 }}
                    animate={{ x: 0, opacity: 1 }}
                    transition={{ delay: index * 0.1 }}
                    className="bg-white rounded-2xl p-4 flex gap-3"
                  >
                    <img
                      src={item.image}
                      alt={item.name}
                      className="w-20 h-20 rounded-xl object-cover"
                    />
                    <div className="flex-1">
                      <h3 className="text-[#2D2B55] font-semibold text-sm mb-1">
                        {item.name}
                      </h3>
                      <p className="text-[#6367FF] font-bold text-base mb-2">
                        Rp {item.price.toLocaleString("id-ID")}
                      </p>
                      <span className="text-[#2D2B55]/60 text-xs bg-[#F8F7FF] px-3 py-1 rounded-full">
                        {item.quantity}x
                      </span>
                    </div>
                    <button
                      onClick={() => removeItem(item.id)}
                      className="self-start p-2 hover:bg-red-50 rounded-lg transition-colors"
                    >
                      <Trash2 className="text-red-500" size={18} />
                    </button>
                  </motion.div>
                ))}
              </div>
            </div>

            {/* Delivery Form */}
            <div className="mb-8">
              <h2 className="text-[#2D2B55] font-bold text-lg mb-4">Detail Pengiriman</h2>
              <div className="space-y-3">
                <input
                  type="text"
                  value={name}
                  onChange={(e) => setName(e.target.value)}
                  placeholder="Nama lengkap"
                  className="w-full px-4 py-3 bg-white rounded-xl text-[#2D2B55] placeholder:text-[#2D2B55]/40 border border-[#2D2B55]/10 focus:outline-none focus:border-[#6367FF] transition-colors"
                />
                <input
                  type="tel"
                  value={phone}
                  onChange={(e) => setPhone(e.target.value)}
                  placeholder="Nomor telepon"
                  className="w-full px-4 py-3 bg-white rounded-xl text-[#2D2B55] placeholder:text-[#2D2B55]/40 border border-[#2D2B55]/10 focus:outline-none focus:border-[#6367FF] transition-colors"
                />
                <textarea
                  value={address}
                  onChange={(e) => setAddress(e.target.value)}
                  placeholder="Alamat pengiriman"
                  rows={3}
                  className="w-full px-4 py-3 bg-white rounded-xl text-[#2D2B55] placeholder:text-[#2D2B55]/40 border border-[#2D2B55]/10 focus:outline-none focus:border-[#6367FF] transition-colors resize-none"
                />
              </div>
            </div>

            {/* Payment */}
            <div className="mb-8">
              <h2 className="text-[#2D2B55] font-bold text-lg mb-4">Pembayaran</h2>
              <div className="space-y-2">
                {paymentMethods.map((method) => {
                  const Icon = method.icon;
                  return (
                    <button
                      key={method.id}
                      onClick={() => setSelectedPayment(method.id)}
                      className={`w-full bg-white rounded-xl p-4 flex items-center gap-3 transition-all ${
                        selectedPayment === method.id
                          ? "border-2 border-[#6367FF]"
                          : "border border-[#2D2B55]/10"
                      }`}
                    >
                      <div className={`w-10 h-10 rounded-lg flex items-center justify-center ${
                        selectedPayment === method.id ? "bg-[#6367FF]" : "bg-[#F8F7FF]"
                      }`}>
                        <Icon className={selectedPayment === method.id ? "text-white" : "text-[#6367FF]"} size={20} />
                      </div>
                      <div className="flex-1 text-left">
                        <p className="text-[#2D2B55] font-semibold text-sm">{method.name}</p>
                        <p className="text-[#2D2B55]/60 text-xs">{method.detail}</p>
                      </div>
                      <ChevronRight className="text-[#2D2B55]/40" size={18} />
                    </button>
                  );
                })}
              </div>
            </div>

            {/* Summary */}
            <div className="mb-6 bg-white rounded-2xl p-5">
              <h2 className="text-[#2D2B55] font-bold text-lg mb-4">Ringkasan</h2>
              <div className="space-y-3 mb-4">
                <div className="flex justify-between text-[#2D2B55]/60 text-sm">
                  <span>Subtotal</span>
                  <span>Rp {subtotal.toLocaleString("id-ID")}</span>
                </div>
                <div className="flex justify-between text-[#2D2B55]/60 text-sm">
                  <span>Pengiriman</span>
                  <span>Rp {deliveryFee.toLocaleString("id-ID")}</span>
                </div>
                <div className="flex justify-between text-[#6367FF] text-sm">
                  <span>Diskon</span>
                  <span>- Rp {discount.toLocaleString("id-ID")}</span>
                </div>
              </div>
              <div className="pt-3 border-t border-[#2D2B55]/10 flex justify-between items-center">
                <span className="text-[#2D2B55] font-bold">Total</span>
                <span className="text-[#6367FF] font-bold text-2xl">Rp {total.toLocaleString("id-ID")}</span>
              </div>
            </div>
          </>
        )}
      </motion.div>

      {/* Fixed Checkout */}
      {cartItems.length > 0 && (
        <div className="fixed bottom-0 left-0 right-0 bg-white border-t border-[#2D2B55]/5 p-6 z-20">
          <button
            onClick={handleCheckout}
            disabled={!name || !phone || !address}
            className="w-full bg-[#6367FF] text-white py-4 rounded-full font-bold active:scale-95 transition-transform disabled:opacity-40 disabled:cursor-not-allowed"
          >
            Checkout
          </button>
        </div>
      )}
    </div>
  );
}