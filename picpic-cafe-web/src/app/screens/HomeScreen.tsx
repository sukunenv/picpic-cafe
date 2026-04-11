import { Search, ChevronRight, Heart, Star, Coffee, Sparkles } from "lucide-react";
import { Link } from "react-router";
import { motion } from "motion/react";
import logo from "figma:asset/c67b6433ddedf46738312a77f1fae7b733129f87.png";

const categories = [
  {
    id: "coffee",
    name: "Coffee",
    image: "https://images.unsplash.com/photo-1631155989916-4aabcabcce8d?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxJbmRvbmVzaWFuJTIwY29mZmVlJTIwbGF0dGV8ZW58MXx8fHwxNzc0ODYzMzkyfDA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral"
  },
  {
    id: "food",
    name: "Food",
    image: "https://images.unsplash.com/photo-1680674814945-7945d913319c?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxuYXNpJTIwZ29yZW5nJTIwcmljZXxlbnwxfHx8fDE3NzQ4NjMzOTJ8MA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral"
  },
  {
    id: "pastry",
    name: "Pastry",
    image: "https://images.unsplash.com/photo-1760661599540-b2fc79b5f8a7?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxjcm9pc3NhbnQlMjBwYXN0cnklMjBmb29kJTIwcGhvdG9ncmFwaHklMjBtaW5pbWFsaXN0fGVufDF8fHx8MTc3NTg4OTMwNHww&ixlib=rb-4.1.0&q=80&w=1080"
  },
  {
    id: "dessert",
    name: "Dessert",
    image: "https://images.unsplash.com/photo-1679942262057-d5732f732841?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxjYWtlJTIwZGVzc2VydHxlbnwxfHx8fDE3NzQ4NjMzOTV8MA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral"
  },
];

const popularMenu = [
  {
    id: "1",
    name: "Cappuccino Premium",
    price: 35000,
    rating: 4.8,
    image: "https://images.unsplash.com/photo-1563390323222-b0593bfc85c3?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxjYXBwdWNjaW5vJTIwY29mZmVlJTIwYXJ0fGVufDF8fHx8MTc3NDgxMjc4OXww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral",
    description: "Rich espresso dengan susu foam halus"
  },
  {
    id: "2",
    name: "Classic Burger",
    price: 48000,
    rating: 4.9,
    image: "https://images.unsplash.com/photo-1625331725309-83e4f3c1373b?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxidXJnZXIlMjBkZWxpY2lvdXN8ZW58MXx8fHwxNzc0ODYxMzMwfDA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral",
    description: "Juicy beef patty dengan keju leleh"
  },
  {
    id: "3",
    name: "Matcha Latte",
    price: 38000,
    rating: 4.7,
    image: "https://images.unsplash.com/photo-1708572727896-117b5ea25a86?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtYXRjaGElMjBsYXR0ZSUyMGdyZWVufGVufDF8fHx8MTc3NDc5ODI2M3ww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral",
    description: "Matcha premium dari Jepang"
  },
  {
    id: "4",
    name: "Fluffy Pancakes",
    price: 42000,
    rating: 4.6,
    image: "https://images.unsplash.com/photo-1550041503-367c95109143?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxwYW5jYWtlJTIwYnJlYWtmYXN0fGVufDF8fHx8MTc3NDg2MzM5M3ww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral",
    description: "Pancake lembut dengan maple syrup"
  },
];

export function HomeScreen() {
  const currentHour = new Date().getHours();
  const greeting = currentHour < 12 ? "Selamat Pagi" : currentHour < 18 ? "Selamat Siang" : "Selamat Malam";

  return (
    <div className="min-h-screen bg-[#F8F7FF]">
      {/* FULL BLEED HERO */}
      <motion.div
        initial={{ opacity: 0 }}
        animate={{ opacity: 1 }}
        transition={{ duration: 0.8 }}
        className="relative h-[70vh] overflow-hidden"
      >
        {/* Hero Image */}
        <div className="absolute inset-0">
          <img
            src="https://images.unsplash.com/photo-1766610953352-69d6f26d7f28?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxjb2ZmZWUlMjBsYXR0ZSUyMGFydCUyMGNhZmUlMjBpbnRlcmlvciUyMHdhcm0lMjBjb3p5fGVufDF8fHx8MTc3NTg4OTMwNHww&ixlib=rb-4.1.0&q=80&w=1080"
            alt="Coffee"
            className="w-full h-full object-cover"
          />
          {/* Gradient Overlay */}
          <div className="absolute inset-0 bg-gradient-to-b from-[#2D2B55]/70 via-[#6367FF]/50 to-[#6367FF]/80" />
        </div>

        {/* Hero Content */}
        <div className="relative h-full flex flex-col justify-between p-6 pt-12">
          {/* Top Bar */}
          <motion.div
            initial={{ y: -20, opacity: 0 }}
            animate={{ y: 0, opacity: 1 }}
            transition={{ delay: 0.2 }}
            className="flex items-center justify-between"
          >
            <div className="flex items-center gap-3">
              <img src={logo} alt="Logo" className="w-12 h-12 rounded-full" />
              <div>
                <p className="text-white/70 text-xs">{greeting}</p>
                <p className="text-white text-sm">Selamat datang kembali</p>
              </div>
            </div>
            <button className="p-2.5 bg-white/20 backdrop-blur-sm rounded-full">
              <Heart className="text-white" size={20} />
            </button>
          </motion.div>

          {/* Brand Section */}
          <motion.div
            initial={{ y: 30, opacity: 0 }}
            animate={{ y: 0, opacity: 1 }}
            transition={{ delay: 0.4 }}
            className="mb-8"
          >
            <h1 className="text-white font-black text-6xl tracking-tight mb-2">PICPIC</h1>
            <p className="text-[#FFDBFD] text-lg font-medium mb-1">kumpul mencerita</p>
            <p className="text-white/80 text-sm max-w-[280px]">Tempat berkumpul sambil menikmati kopi dan cerita hangat</p>

            {/* Search */}
            <div className="mt-6 relative">
              <Search className="absolute left-4 top-1/2 -translate-y-1/2 text-[#2D2B55]/50" size={20} />
              <input
                type="text"
                placeholder="Cari menu..."
                className="w-full pl-12 pr-4 py-3.5 rounded-full bg-white/95 backdrop-blur-sm text-[#2D2B55] placeholder:text-[#2D2B55]/50 border-none focus:outline-none focus:ring-2 focus:ring-[#FFDBFD]"
              />
            </div>
          </motion.div>
        </div>
      </motion.div>

      {/* PROMO STRIP */}
      <motion.div
        initial={{ y: 20, opacity: 0 }}
        animate={{ y: 0, opacity: 1 }}
        transition={{ delay: 0.6 }}
        className="-mt-6 mx-6 mb-8"
      >
        <Link to="/menu" className="block relative overflow-hidden rounded-3xl group">
          <div className="absolute inset-0 bg-gradient-to-r from-[#6367FF] to-[#8494FF]" />
          <div className="relative px-6 py-5 flex items-center justify-between">
            <div>
              <div className="inline-flex items-center gap-1.5 bg-[#FFDBFD] text-[#2D2B55] px-3 py-1 rounded-full text-xs font-bold mb-2">
                <Sparkles size={12} />
                Promo Hari Ini
              </div>
              <p className="text-white font-bold text-xl">Buy 1 Get 1 Free</p>
              <p className="text-white/90 text-sm">Semua minuman coffee</p>
            </div>
            <ChevronRight className="text-white group-hover:translate-x-1 transition-transform" size={24} />
          </div>
        </Link>
      </motion.div>

      {/* CATEGORIES - FULL WIDTH */}
      <motion.div
        initial={{ y: 30, opacity: 0 }}
        animate={{ y: 0, opacity: 1 }}
        transition={{ delay: 0.7 }}
        className="mb-12"
      >
        <div className="px-6 mb-4">
          <h2 className="text-[#2D2B55] font-bold text-2xl">Kategori</h2>
        </div>
        <div className="grid grid-cols-2 gap-0">
          {categories.map((category, index) => (
            <Link
              key={category.id}
              to={`/menu?category=${category.id}`}
              className="relative aspect-square overflow-hidden group"
            >
              <motion.div
                initial={{ opacity: 0, scale: 0.9 }}
                animate={{ opacity: 1, scale: 1 }}
                transition={{ delay: 0.8 + index * 0.1 }}
                className="h-full"
              >
                <img
                  src={category.image}
                  alt={category.name}
                  className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-[#2D2B55]/80 via-[#2D2B55]/30 to-transparent group-hover:from-[#6367FF]/80 transition-all duration-500" />
                <div className="absolute inset-0 flex items-end p-6">
                  <p className="text-white font-bold text-2xl">{category.name}</p>
                </div>
              </motion.div>
            </Link>
          ))}
        </div>
      </motion.div>

      {/* POPULAR MENU - IMAGE LED */}
      <motion.div
        initial={{ y: 30, opacity: 0 }}
        animate={{ y: 0, opacity: 1 }}
        transition={{ delay: 1 }}
        className="px-6 mb-24"
      >
        <div className="flex items-end justify-between mb-6">
          <div>
            <h2 className="text-[#2D2B55] font-bold text-2xl">Menu Populer</h2>
            <p className="text-[#2D2B55]/60 text-sm mt-1">Paling disukai pelanggan</p>
          </div>
          <Link to="/menu" className="text-[#6367FF] font-bold text-sm flex items-center gap-1">
            Lihat Semua
            <ChevronRight size={18} />
          </Link>
        </div>

        <div className="space-y-4">
          {popularMenu.slice(0, 3).map((item, index) => (
            <motion.div
              key={item.id}
              initial={{ x: -30, opacity: 0 }}
              animate={{ x: 0, opacity: 1 }}
              transition={{ delay: 1.1 + index * 0.1 }}
            >
              <Link
                to={`/product/${item.id}`}
                className="flex gap-4 group"
              >
                <div className="relative w-28 h-28 flex-shrink-0 rounded-2xl overflow-hidden">
                  <img
                    src={item.image}
                    alt={item.name}
                    className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                  />
                  <div className="absolute top-2 right-2">
                    <div className="bg-white/95 backdrop-blur-sm px-2 py-1 rounded-full flex items-center gap-1">
                      <Star size={12} className="text-[#FFDBFD] fill-[#FFDBFD]" />
                      <span className="text-[#2D2B55] text-xs font-bold">{item.rating}</span>
                    </div>
                  </div>
                </div>
                <div className="flex-1 flex flex-col justify-center">
                  <h3 className="text-[#2D2B55] font-bold text-lg mb-1">{item.name}</h3>
                  <p className="text-[#2D2B55]/60 text-sm mb-2 line-clamp-1">{item.description}</p>
                  <p className="text-[#6367FF] font-bold text-lg">Rp {item.price.toLocaleString("id-ID")}</p>
                </div>
                <div className="flex items-center">
                  <div className="p-2 bg-[#6367FF] rounded-full group-hover:scale-110 transition-transform">
                    <ChevronRight className="text-white" size={20} />
                  </div>
                </div>
              </Link>
            </motion.div>
          ))}
        </div>
      </motion.div>
    </div>
  );
}