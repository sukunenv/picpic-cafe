import { useState } from "react";
import { ArrowLeft, Heart, Minus, Plus, ShoppingCart, Star, Clock } from "lucide-react";
import { useParams, useNavigate } from "react-router";
import { motion } from "motion/react";

const productData: Record<string, any> = {
  "1": {
    id: "1",
    name: "Cappuccino Premium",
    price: 35000,
    description: "Espresso berkualitas tinggi dengan susu berbusa lembut dan taburan cokelat. Sempurna untuk menemani obrolan santai bersama teman.",
    rating: 4.8,
    reviews: 124,
    preparationTime: "10-15 min",
    image: "https://images.unsplash.com/photo-1563390323222-b0593bfc85c3?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxjYXBwdWNjaW5vJTIwY29mZmVlJTIwYXJ0fGVufDF8fHx8MTc3NDgxMjc4OXww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral",
    ingredients: ["Espresso", "Susu", "Cokelat", "Gula"],
  },
  "2": {
    id: "2",
    name: "Classic Burger",
    price: 48000,
    description: "Burger juicy dengan daging sapi premium, keju leleh, sayuran segar, dan saus spesial. Disajikan dengan kentang goreng.",
    rating: 4.9,
    reviews: 203,
    preparationTime: "15-20 min",
    image: "https://images.unsplash.com/photo-1625331725309-83e4f3c1373b?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxidXJnZXIlMjBkZWxpY2lvdXN8ZW58MXx8fHwxNzc0ODYxMzMwfDA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral",
    ingredients: ["Daging Sapi", "Keju", "Sayuran", "Saus", "Kentang"],
  },
  "3": {
    id: "3",
    name: "Matcha Latte",
    price: 38000,
    description: "Teh hijau matcha pilihan dari Jepang dengan susu creamy. Kaya antioksidan dan rasa yang menenangkan.",
    rating: 4.7,
    reviews: 156,
    preparationTime: "10-15 min",
    image: "https://images.unsplash.com/photo-1708572727896-117b5ea25a86?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtYXRjaGElMjBsYXR0ZSUyMGdyZWVufGVufDF8fHx8MTc3NDc5ODI2M3ww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral",
    ingredients: ["Matcha", "Susu", "Gula", "Es"],
  },
  "4": {
    id: "4",
    name: "Fluffy Pancakes",
    price: 42000,
    description: "Pancake super lembut dengan maple syrup asli, butter, dan topping buah segar. Sempurna untuk sarapan atau dessert.",
    rating: 4.6,
    reviews: 98,
    preparationTime: "15-20 min",
    image: "https://images.unsplash.com/photo-1550041503-367c95109143?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxwYW5jYWtlJTIwYnJlYWtmYXN0fGVufDF8fHx8MTc3NDg2MzM5M3ww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral",
    ingredients: ["Tepung", "Telur", "Susu", "Buah", "Maple Syrup"],
  },
};

export function ProductDetail() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [quantity, setQuantity] = useState(1);
  const [isFavorite, setIsFavorite] = useState(false);

  const product = productData[id || "1"] || productData["1"];

  const handleAddToCart = () => {
    // Add to cart logic here
    navigate("/cart");
  };

  return (
    <div className="min-h-screen bg-[#F8F7FF]">
      {/* Full Screen Hero Image */}
      <motion.div
        initial={{ opacity: 0 }}
        animate={{ opacity: 1 }}
        className="relative h-[55vh]"
      >
        <img
          src={product.image}
          alt={product.name}
          className="w-full h-full object-cover"
        />
        <div className="absolute inset-0 bg-gradient-to-t from-[#2D2B55]/70 via-transparent to-[#2D2B55]/30" />

        {/* Top Navigation */}
        <div className="absolute top-0 left-0 right-0 p-6 flex items-center justify-between">
          <button
            onClick={() => navigate(-1)}
            className="p-2.5 bg-white/95 backdrop-blur-sm rounded-full"
          >
            <ArrowLeft className="text-[#2D2B55]" size={22} />
          </button>
          <button
            onClick={() => setIsFavorite(!isFavorite)}
            className="p-2.5 bg-white/95 backdrop-blur-sm rounded-full"
          >
            <Heart
              className={isFavorite ? "text-[#6367FF] fill-[#6367FF]" : "text-[#2D2B55]"}
              size={22}
            />
          </button>
        </div>

        {/* Info Badges */}
        <div className="absolute bottom-6 left-6 right-6 flex gap-3">
          <div className="flex items-center gap-2 bg-white/95 backdrop-blur-sm px-4 py-2 rounded-full">
            <Star size={16} className="text-[#FFDBFD] fill-[#FFDBFD]" />
            <span className="text-[#2D2B55] font-bold text-sm">{product.rating}</span>
            <span className="text-[#2D2B55]/60 text-sm">({product.reviews})</span>
          </div>
          <div className="flex items-center gap-2 bg-white/95 backdrop-blur-sm px-4 py-2 rounded-full">
            <Clock size={16} className="text-[#6367FF]" />
            <span className="text-[#2D2B55] font-bold text-sm">{product.preparationTime}</span>
          </div>
        </div>
      </motion.div>

      {/* Content */}
      <motion.div
        initial={{ y: 30, opacity: 0 }}
        animate={{ y: 0, opacity: 1 }}
        transition={{ delay: 0.2 }}
        className="px-6 py-8 pb-36"
      >
        {/* Product Name & Price */}
        <div className="mb-8">
          <h1 className="text-[#2D2B55] font-black text-4xl mb-4 leading-tight">
            {product.name}
          </h1>
          <p className="text-[#6367FF] font-bold text-3xl">
            Rp {product.price.toLocaleString("id-ID")}
          </p>
        </div>

        {/* Description */}
        <div className="mb-8">
          <h2 className="text-[#2D2B55] font-bold text-lg mb-3">Tentang</h2>
          <p className="text-[#2D2B55]/70 text-base leading-relaxed">
            {product.description}
          </p>
        </div>

        {/* Ingredients */}
        <div className="mb-8">
          <h2 className="text-[#2D2B55] font-bold text-lg mb-3">Bahan</h2>
          <div className="flex flex-wrap gap-2">
            {product.ingredients.map((ingredient: string) => (
              <span
                key={ingredient}
                className="px-4 py-2 bg-[#C9BEFF]/20 rounded-full text-[#2D2B55] text-sm font-medium"
              >
                {ingredient}
              </span>
            ))}
          </div>
        </div>

        {/* Quantity */}
        <div className="mb-6">
          <h2 className="text-[#2D2B55] font-bold text-lg mb-4">Jumlah</h2>
          <div className="flex items-center justify-center gap-8">
            <button
              onClick={() => setQuantity(Math.max(1, quantity - 1))}
              className="w-12 h-12 bg-[#F8F7FF] rounded-full flex items-center justify-center border-2 border-[#2D2B55]/10 active:scale-95 transition-transform"
            >
              <Minus className="text-[#2D2B55]" size={20} />
            </button>
            <span className="text-[#2D2B55] font-bold text-3xl w-16 text-center">
              {quantity}
            </span>
            <button
              onClick={() => setQuantity(quantity + 1)}
              className="w-12 h-12 bg-[#6367FF] rounded-full flex items-center justify-center active:scale-95 transition-transform"
            >
              <Plus className="text-white" size={20} />
            </button>
          </div>
        </div>
      </motion.div>

      {/* Fixed CTA */}
      <div className="fixed bottom-0 left-0 right-0 bg-white border-t border-[#2D2B55]/5 p-6 z-20">
        <div className="flex items-center gap-4">
          <div className="flex-1">
            <p className="text-[#2D2B55]/60 text-xs mb-1">Total</p>
            <p className="text-[#2D2B55] font-bold text-xl">
              Rp {(product.price * quantity).toLocaleString("id-ID")}
            </p>
          </div>
          <button
            onClick={handleAddToCart}
            className="flex-1 bg-[#6367FF] text-white py-4 rounded-full font-bold text-base flex items-center justify-center gap-2 active:scale-95 transition-transform"
          >
            <ShoppingCart size={20} />
            Tambah
          </button>
        </div>
      </div>
    </div>
  );
}