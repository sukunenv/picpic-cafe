import { useState } from "react";
import { Search, Heart, ArrowLeft, Star } from "lucide-react";
import { Link, useSearchParams } from "react-router";
import { motion } from "motion/react";

const categories = [
  { id: "all", name: "Semua" },
  { id: "coffee", name: "Coffee" },
  { id: "food", name: "Food" },
  { id: "pastry", name: "Pastry" },
  { id: "dessert", name: "Dessert" },
];

const menuItems = [
  {
    id: "1",
    name: "Cappuccino Premium",
    price: 35000,
    category: "coffee",
    rating: 4.8,
    image: "https://images.unsplash.com/photo-1563390323222-b0593bfc85c3?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxjYXBwdWNjaW5vJTIwY29mZmVlJTIwYXJ0fGVufDF8fHx8MTc3NDgxMjc4OXww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral",
  },
  {
    id: "2",
    name: "Classic Burger",
    price: 48000,
    category: "food",
    rating: 4.9,
    image: "https://images.unsplash.com/photo-1625331725309-83e4f3c1373b?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxidXJnZXIlMjBkZWxpY2lvdXN8ZW58MXx8fHwxNzc0ODYxMzMwfDA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral",
  },
  {
    id: "3",
    name: "Matcha Latte",
    price: 38000,
    category: "coffee",
    rating: 4.7,
    image: "https://images.unsplash.com/photo-1708572727896-117b5ea25a86?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtYXRjaGElMjBsYXR0ZSUyMGdyZWVufGVufDF8fHx8MTc3NDc5ODI2M3ww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral",
  },
  {
    id: "4",
    name: "Fluffy Pancakes",
    price: 42000,
    category: "food",
    rating: 4.6,
    image: "https://images.unsplash.com/photo-1550041503-367c95109143?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxwYW5jYWtlJTIwYnJlYWtmYXN0fGVufDF8fHx8MTc3NDg2MzM5M3ww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral",
  },
  {
    id: "5",
    name: "Croissant",
    price: 28000,
    category: "pastry",
    rating: 4.5,
    image: "https://images.unsplash.com/photo-1712723247648-64a03ba7c333?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxjcm9pc3NhbnQlMjBwYXN0cnl8ZW58MXx8fHwxNzc0ODYwMTIxfDA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral",
  },
  {
    id: "6",
    name: "Chocolate Cake",
    price: 45000,
    category: "dessert",
    rating: 4.9,
    image: "https://images.unsplash.com/photo-1679942262057-d5732f732841?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxjYWtlJTIwZGVzc2VydHxlbnwxfHx8fDE3NzQ4NjMzOTV8MA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral",
  },
  {
    id: "7",
    name: "Fresh Sandwich",
    price: 38000,
    category: "food",
    rating: 4.7,
    image: "https://images.unsplash.com/photo-1763647814142-b1eb054d42f1?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxzYW5kd2ljaCUyMGZyZXNofGVufDF8fHx8MTc3NDg2MzM5M3ww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral",
  },
  {
    id: "8",
    name: "Pasta Carbonara",
    price: 52000,
    category: "food",
    rating: 4.8,
    image: "https://images.unsplash.com/photo-1609166639722-47053ca112ea?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxwYXN0YSUyMEl0YWxpYW4lMjBmb29kfGVufDF8fHx8MTc3NDg2MzM5NHww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral",
  },
];

export function MenuScreen() {
  const [searchParams] = useSearchParams();
  const categoryParam = searchParams.get("category");
  const [selectedCategory, setSelectedCategory] = useState(categoryParam || "all");

  const filteredItems =
    selectedCategory === "all"
      ? menuItems
      : menuItems.filter((item) => item.category === selectedCategory);

  return (
    <div className="min-h-screen pb-24 bg-[#F8F7FF]">
      {/* Clean Header */}
      <div className="bg-white sticky top-0 z-20 border-b border-[#2D2B55]/5">
        <div className="px-6 pt-12 pb-4">
          <div className="flex items-center gap-4 mb-4">
            <Link to="/" className="p-2 hover:bg-[#F8F7FF] rounded-full transition-colors">
              <ArrowLeft className="text-[#2D2B55]" size={24} />
            </Link>
            <h1 className="text-[#2D2B55] font-bold text-2xl flex-1">Menu</h1>
            <button className="p-2 hover:bg-[#F8F7FF] rounded-full transition-colors">
              <Heart className="text-[#6367FF]" size={24} />
            </button>
          </div>

          {/* Search */}
          <div className="relative">
            <Search className="absolute left-4 top-1/2 -translate-y-1/2 text-[#2D2B55]/40" size={20} />
            <input
              type="text"
              placeholder="Cari menu..."
              className="w-full pl-12 pr-4 py-3 rounded-full bg-[#F8F7FF] text-[#2D2B55] placeholder:text-[#2D2B55]/40 border-none focus:outline-none focus:ring-2 focus:ring-[#6367FF]/20"
            />
          </div>
        </div>

        {/* Filter Pills */}
        <div className="px-6 pb-4 overflow-x-auto scrollbar-hide">
          <div className="flex gap-2">
            {categories.map((category) => (
              <button
                key={category.id}
                onClick={() => setSelectedCategory(category.id)}
                className={`px-5 py-2 rounded-full whitespace-nowrap text-sm font-medium transition-all ${
                  selectedCategory === category.id
                    ? "bg-[#6367FF] text-white"
                    : "bg-[#F8F7FF] text-[#2D2B55]/60 hover:bg-[#C9BEFF]/30"
                }`}
              >
                {category.name}
              </button>
            ))}
          </div>
        </div>
      </div>

      {/* Menu Grid */}
      <div className="px-6 mt-6">
        <p className="text-[#2D2B55]/60 text-sm mb-4">
          {filteredItems.length} item
        </p>
        <div className="grid grid-cols-2 gap-4">
          {filteredItems.map((item, index) => (
            <motion.div
              key={item.id}
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: index * 0.05 }}
            >
              <Link
                to={`/product/${item.id}`}
                className="block group"
              >
                <div className="relative aspect-[3/4] rounded-2xl overflow-hidden mb-3">
                  <img
                    src={item.image}
                    alt={item.name}
                    className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-[#2D2B55]/60 via-transparent to-transparent" />

                  {/* Rating Badge */}
                  <div className="absolute top-3 left-3">
                    <div className="bg-white/95 backdrop-blur-sm px-2.5 py-1 rounded-full flex items-center gap-1">
                      <Star size={12} className="text-[#FFDBFD] fill-[#FFDBFD]" />
                      <span className="text-[#2D2B55] text-xs font-bold">{item.rating}</span>
                    </div>
                  </div>

                  {/* Heart */}
                  <button className="absolute top-3 right-3 p-2 bg-white/95 backdrop-blur-sm rounded-full opacity-0 group-hover:opacity-100 transition-opacity">
                    <Heart size={14} className="text-[#6367FF]" />
                  </button>

                  {/* Price on Image */}
                  <div className="absolute bottom-3 left-3 right-3">
                    <p className="text-white font-bold text-lg">Rp {item.price.toLocaleString("id-ID")}</p>
                  </div>
                </div>

                <h3 className="text-[#2D2B55] font-semibold text-sm line-clamp-2 leading-tight">
                  {item.name}
                </h3>
              </Link>
            </motion.div>
          ))}
        </div>
      </div>
    </div>
  );
}