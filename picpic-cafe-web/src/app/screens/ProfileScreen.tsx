import { User, History, Settings, LogOut, Gift, ChevronRight, Star, Award, TrendingUp } from "lucide-react";
import { Link } from "react-router";
import { motion } from "motion/react";
import logo from "figma:asset/c67b6433ddedf46738312a77f1fae7b733129f87.png";

const orderHistory = [
  {
    id: "ORD001",
    date: "28 Mar 2026",
    items: 2,
    total: 83000,
    status: "Selesai",
    image: "https://images.unsplash.com/photo-1563390323222-b0593bfc85c3?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxjYXBwdWNjaW5vJTIwY29mZmVlJTIwYXJ0fGVufDF8fHx8MTc3NDgxMjc4OXww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral",
  },
  {
    id: "ORD002",
    date: "25 Mar 2026",
    items: 1,
    total: 48000,
    status: "Selesai",
    image: "https://images.unsplash.com/photo-1625331725309-83e4f3c1373b?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxidXJnZXIlMjBkZWxpY2lvdXN8ZW58MXx8fHwxNzc0ODYxMzMwfDA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral",
  },
  {
    id: "ORD003",
    date: "22 Mar 2026",
    items: 3,
    total: 124000,
    status: "Selesai",
    image: "https://images.unsplash.com/photo-1708572727896-117b5ea25a86?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtYXRjaGElMjBsYXR0ZSUyMGdyZWVufGVufDF8fHx8MTc3NDc5ODI2M3ww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral",
  },
];

const menuItems = [
  { icon: Settings, label: "Pengaturan", path: "#", color: "from-[#6367FF] to-[#8494FF]" },
  { icon: Gift, label: "Rewards & Promo", path: "#", color: "from-[#C9BEFF] to-[#FFDBFD]" },
  { icon: Star, label: "Berikan Rating", path: "#", color: "from-[#FFDBFD] to-[#C9BEFF]" },
];

export function ProfileScreen() {
  return (
    <div className="min-h-screen pb-24 bg-[#F8F7FF]">
      {/* Header with Loyalty Card */}
      <motion.div
        initial={{ opacity: 0 }}
        animate={{ opacity: 1 }}
        className="bg-gradient-to-br from-[#6367FF] to-[#8494FF] pt-14 pb-24 px-6 relative overflow-hidden"
      >
        <div className="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAxMCAwIEwgMCAwIDAgMTAiIGZpbGw9Im5vbmUiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS13aWR0aD0iMC41IiBvcGFjaXR5PSIwLjEiLz48L3BhdHRlcm4+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JpZCkiLz48L3N2Zz4=')] opacity-20" />

        <motion.div
          initial={{ y: -20, opacity: 0 }}
          animate={{ y: 0, opacity: 1 }}
          transition={{ delay: 0.2 }}
          className="relative z-10"
        >
          <h1 className="text-white font-bold text-2xl mb-6">Profil</h1>

          {/* Profile Info */}
          <div className="flex items-center gap-4 mb-6">
            <div className="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
              <User className="text-white" size={32} />
            </div>
            <div className="flex-1">
              <h2 className="text-white font-bold text-xl mb-1">Pengguna PICPIC</h2>
              <p className="text-white/80 text-sm">picpic@example.com</p>
            </div>
            <button className="p-2 bg-white/20 backdrop-blur-sm rounded-full">
              <ChevronRight className="text-white" size={20} />
            </button>
          </div>

          {/* Loyalty Card */}
          <div className="bg-white rounded-2xl p-6">
            <div className="flex items-center justify-between mb-4">
              <div className="flex items-center gap-2">
                <img src={logo} alt="PICPIC" className="w-8 h-8 rounded-lg" />
                <span className="text-[#2D2B55] font-bold">Points</span>
              </div>
              <Gift className="text-[#6367FF]" size={20} />
            </div>
            <div className="flex items-baseline gap-2">
              <span className="text-[#6367FF] font-black text-4xl">1,250</span>
              <span className="text-[#2D2B55]/60 text-sm">poin tersedia</span>
            </div>
          </div>
        </motion.div>
      </motion.div>

      {/* Content */}
      <motion.div
        initial={{ y: 30, opacity: 0 }}
        animate={{ y: 0, opacity: 1 }}
        transition={{ delay: 0.3 }}
        className="px-6 -mt-12 relative z-10"
      >
        {/* Order History */}
        <div className="mb-8">
          <div className="flex items-center justify-between mb-4">
            <h2 className="text-[#2D2B55] font-bold text-lg">Pesanan Terakhir</h2>
            <button className="text-[#6367FF] text-sm font-semibold">Lihat Semua</button>
          </div>
          <div className="space-y-3">
            {orderHistory.map((order, index) => (
              <motion.div
                key={order.id}
                initial={{ x: -20, opacity: 0 }}
                animate={{ x: 0, opacity: 1 }}
                transition={{ delay: 0.4 + index * 0.1 }}
                className="bg-white rounded-2xl p-4 flex gap-3"
              >
                <img
                  src={order.image}
                  alt="Order"
                  className="w-16 h-16 rounded-xl object-cover"
                />
                <div className="flex-1">
                  <div className="flex items-start justify-between mb-1">
                    <div>
                      <h3 className="text-[#2D2B55] font-semibold text-sm">{order.id}</h3>
                      <p className="text-[#2D2B55]/60 text-xs">{order.date}</p>
                    </div>
                    <span className="text-[#6367FF] bg-[#6367FF]/10 px-2 py-1 rounded-full text-xs font-semibold">
                      {order.status}
                    </span>
                  </div>
                  <div className="flex items-center justify-between mt-2">
                    <span className="text-[#2D2B55]/60 text-xs">{order.items} item</span>
                    <span className="text-[#2D2B55] font-bold text-sm">
                      Rp {order.total.toLocaleString("id-ID")}
                    </span>
                  </div>
                </div>
              </motion.div>
            ))}
          </div>
        </div>

        {/* Menu Options */}
        <div className="mb-8">
          <h2 className="text-[#2D2B55] font-bold text-lg mb-4">Lainnya</h2>
          <div className="space-y-2">
            {menuItems.map((item, index) => {
              const Icon = item.icon;
              return (
                <motion.div
                  key={item.label}
                  initial={{ x: -20, opacity: 0 }}
                  animate={{ x: 0, opacity: 1 }}
                  transition={{ delay: 0.7 + index * 0.1 }}
                >
                  <Link
                    to={item.path}
                    className="flex items-center gap-3 px-4 py-4 bg-white rounded-xl hover:bg-white/80 transition-colors"
                  >
                    <div className="w-10 h-10 bg-[#F8F7FF] rounded-lg flex items-center justify-center">
                      <Icon className="text-[#6367FF]" size={20} />
                    </div>
                    <span className="text-[#2D2B55] font-semibold flex-1 text-sm">{item.label}</span>
                    <ChevronRight className="text-[#2D2B55]/40" size={18} />
                  </Link>
                </motion.div>
              );
            })}
          </div>
        </div>

        {/* Logout */}
        <button className="w-full bg-white border border-red-500/20 text-red-500 py-3 rounded-full font-semibold text-sm flex items-center justify-center gap-2 hover:bg-red-50 transition-colors">
          <LogOut size={18} />
          Keluar
        </button>
      </motion.div>
    </div>
  );
}